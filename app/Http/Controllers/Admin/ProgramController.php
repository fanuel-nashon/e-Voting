<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('faculty')->get();

        return response()->json([
            'success'  => true,
            'programs' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'faculty_id' => 'nullable|exists:faculties,id',
        ]);

        $program = Program::create([
            'name'       => $request->name,
            'faculty_id' => $request->faculty_id ?: null,
        ]);

        $program->load('faculty');

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'program' => $program,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'faculty_id' => 'nullable|exists:faculties,id',
        ]);

        $program = Program::findOrFail($id);
        $program->update([
            'name'       => $request->name,
            'faculty_id' => $request->faculty_id ?: null,
        ]);

        $program->load('faculty');

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'program' => $program,
        ]);
    }

    public function destroy(string $id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
        ]);
    }
}
