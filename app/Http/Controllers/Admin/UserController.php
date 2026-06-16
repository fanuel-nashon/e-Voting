<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'faculty', 'student.faculty', 'student.program'])->get()->map(function ($user) {
            return [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'role'            => $user->roles->first()?->name ?? 'none',
                'faculty_id'      => $user->faculty_id,
                'faculty'         => $user->faculty?->name,
                'student_id'      => $user->student?->id,
                'student_reg_no'  => $user->student?->reg_no,
                'student_name'    => $user->student?->name,
                'student_faculty' => $user->student?->faculty?->name,
                'student_program' => $user->student?->program?->name,
                'student_faculty_id' => $user->student?->faculty_id,
                'student_program_id' => $user->student?->program_id,
            ];
        });

        $faculties = Faculty::orderBy('name')->get(['id', 'name']);
        $programs  = Program::with('faculty')->orderBy('name')->get(['id', 'name', 'faculty_id']);

        return view('users.index', compact('users', 'faculties', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:191',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'role'       => 'required|in:admin,election_admin,voter',
            'faculty_id' => 'nullable|exists:faculties,id',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'faculty_id' => $request->role === 'election_admin' ? $request->faculty_id : null,
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $request->role,
                'faculty_id' => $user->faculty_id,
                'faculty'    => $user->faculty?->name,
            ],
        ], 201);
    }

    public function assignFaculty(Request $request, User $user)
    {
        $request->validate(['faculty_id' => 'nullable|exists:faculties,id']);
        $user->update(['faculty_id' => $request->faculty_id ?: null]);

        return response()->json([
            'success' => true,
            'faculty' => $user->fresh()->faculty?->name,
        ]);
    }

    public function attachStudent(Request $request, User $user)
    {
        $request->validate([
            'name'       => 'required|string|max:191',
            'reg_no'     => ['required', 'string', 'max:191', Rule::unique('students', 'reg_no')->ignore($user->student?->id)],
            'faculty_id' => 'required|exists:faculties,id',
            'program_id' => 'required|exists:programs,id',
        ]);

        $student = Student::updateOrCreate(
            ['user_id' => $user->id],
            [
                'reg_no'     => $request->reg_no,
                'name'       => $request->name,
                'faculty_id' => $request->faculty_id,
                'program_id' => $request->program_id,
            ]
        );

        $student->load(['faculty', 'program']);

        return response()->json([
            'success'            => true,
            'message'            => 'Student profile saved.',
            'student_id'         => $student->id,
            'student_reg_no'     => $student->reg_no,
            'student_name'       => $student->name,
            'student_faculty'    => $student->faculty?->name,
            'student_program'    => $student->program?->name,
            'student_faculty_id' => $student->faculty_id,
            'student_program_id' => $student->program_id,
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }
}
