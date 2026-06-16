<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Faculty;
use App\Models\Position;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    private const POSITION_NAMES = [
        'president'   => 'President',
        'faculty_rep' => 'Faculty Representative',
        'senator'     => 'Senator',
        'class_rep'   => 'Class Representative',
    ];

    public function index()
    {
        $candidates = Candidate::with(['position.faculty', 'position.program.faculty'])->get();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'candidates' => $candidates]);
        }

        $faculties = Faculty::all();
        $programs  = Program::with('faculty')->get();

        return view('pages.candidates', compact('candidates', 'faculties', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'image'         => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'position_type' => 'required|in:president,faculty_rep,senator,class_rep',
            'faculty_id'    => 'required_if:position_type,faculty_rep,senator|nullable|exists:faculties,id',
            'program_id'    => 'required_if:position_type,class_rep|nullable|exists:programs,id',
        ]);

        $imagePath = $request->file('image')->store('candidates', 'public');

        $position = $this->findOrCreatePosition($request->position_type, $request->faculty_id, $request->program_id);

        $candidate = Candidate::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'image'       => $imagePath,
            'position_id' => $position->id,
        ]);

        $candidate->load(['position.faculty', 'position.program.faculty']);

        return response()->json([
            'success'   => true,
            'message'   => 'Candidate created successfully',
            'candidate' => $candidate,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'position_type' => 'required|in:president,faculty_rep,senator,class_rep',
            'faculty_id'    => 'required_if:position_type,faculty_rep,senator|nullable|exists:faculties,id',
            'program_id'    => 'required_if:position_type,class_rep|nullable|exists:programs,id',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($candidate->image);
            $candidate->image = $request->file('image')->store('candidates', 'public');
        }

        $position = $this->findOrCreatePosition($request->position_type, $request->faculty_id, $request->program_id);

        $candidate->name        = $request->name;
        $candidate->email       = $request->email;
        $candidate->position_id = $position->id;
        $candidate->save();

        $candidate->load(['position.faculty', 'position.program.faculty']);

        return response()->json([
            'success'   => true,
            'message'   => 'Candidate updated successfully',
            'candidate' => $candidate,
        ]);
    }

    public function destroy(string $id)
    {
        $candidate = Candidate::findOrFail($id);
        Storage::disk('public')->delete($candidate->image);
        $candidate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Candidate deleted successfully',
        ]);
    }

    private function findOrCreatePosition(string $type, ?string $facultyId, ?string $programId): Position
    {
        $facultyId  = in_array($type, ['faculty_rep', 'senator']) ? $facultyId : null;
        $programId  = $type === 'class_rep' ? $programId : null;

        return Position::firstOrCreate(
            ['type' => $type, 'faculty_id' => $facultyId, 'program_id' => $programId],
            ['name' => self::POSITION_NAMES[$type]]
        );
    }
}
