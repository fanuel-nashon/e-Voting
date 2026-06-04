<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'faculty'])->get()->map(function ($user) {
            return [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->roles->first()?->name ?? 'none',
                'faculty_id' => $user->faculty_id,
                'faculty'    => $user->faculty?->name,
            ];
        });

        $faculties = Faculty::orderBy('name')->get(['id', 'name']);

        return view('users.index', compact('users', 'faculties'));
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
