<?php

namespace App\Http\Controllers;

use App\Mail\VoterCredentialsMail;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Models\VoterRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VoterRegistrationController extends Controller
{
    public function showForm()
    {
        $programs = Program::with('faculty')->orderBy('name')->get();
        return view('auth.voter-register', compact('programs'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:191',
            'personal_email' => 'required|email|max:191|unique:voter_registrations,personal_email',
            'reg_number'     => 'required|string|max:50|unique:voter_registrations,reg_number|unique:students,reg_no',
            'program_id'     => 'required|exists:programs,id',
            'photo'          => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $program   = Program::with('faculty')->findOrFail($request->program_id);
        $regYear   = VoterRegistration::extractYear($request->reg_number);
        $loginEmail = VoterRegistration::buildEmail($request->name, $regYear);
        $photoPath = $request->file('photo')->store('voter-photos', 'public');

        VoterRegistration::create([
            'name'           => $request->name,
            'email'          => $loginEmail,
            'personal_email' => $request->personal_email,
            'reg_number'     => strtoupper(trim($request->reg_number)),
            'reg_year'       => $regYear,
            'program_id'     => $program->id,
            'faculty_id'     => $program->faculty_id,
            'photo'          => $photoPath,
            'status'         => 'pending',
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Registration submitted. Await approval from your Faculty Election Admin.',
            'login_email' => $loginEmail,
        ]);
    }

    public function pendingList()
    {
        $user  = auth()->user();
        $query = VoterRegistration::with(['program', 'faculty'])->where('status', 'pending');

        if ($user->hasRole('election_admin') && $user->faculty_id) {
            $query->where('faculty_id', $user->faculty_id);
        }

        return response()->json(['success' => true, 'registrations' => $query->orderByDesc('created_at')->get()]);
    }

    public function approve(VoterRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already processed.'], 422);
        }

        $plainPassword = Str::random(10);

        $user = User::create([
            'name'           => $registration->name,
            'email'          => $registration->email,          // login email (generated)
            'personal_email' => $registration->personal_email, // real email for communications
            'password'       => Hash::make($plainPassword),
            'faculty_id'     => $registration->faculty_id,
        ]);
        $user->assignRole('voter');

        Student::create([
            'name'       => $registration->name,
            'reg_no'     => $registration->reg_number,
            'faculty_id' => $registration->faculty_id,
            'program_id' => $registration->program_id,
            'user_id'    => $user->id,
        ]);

        $registration->update([
            'status'       => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Send credentials to the student's PERSONAL email
        Mail::to($registration->personal_email)->send(new VoterCredentialsMail(
            voterName:     $registration->name,
            email:         $registration->email,    // login email shown in the email body
            plainPassword: $plainPassword,
            faculty:       $registration->faculty->name,
            program:       $registration->program->name,
        ));

        return response()->json([
            'success' => true,
            'message' => "Approved. Credentials sent to {$registration->personal_email}.",
        ]);
    }

    public function reject(Request $request, VoterRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already processed.'], 422);
        }

        $registration->update([
            'status'           => 'rejected',
            'processed_by'     => auth()->id(),
            'processed_at'     => now(),
            'rejection_reason' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Registration rejected.']);
    }
}
