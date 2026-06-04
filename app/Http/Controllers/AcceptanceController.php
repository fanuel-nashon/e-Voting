<?php

namespace App\Http\Controllers;

use App\Models\CandidateAcceptance;
use Illuminate\Http\Request;

class AcceptanceController extends Controller
{
    public function form(string $token)
    {
        $acceptance = CandidateAcceptance::with(['candidate', 'position'])
            ->where('token', $token)
            ->firstOrFail();

        if ($acceptance->responded_at) {
            return view('acceptance.form', ['acceptance' => $acceptance, 'alreadyResponded' => true]);
        }

        $deadline = \App\Models\ElectionSetting::current()->acceptance_deadline_at;
        $expired  = $deadline && now()->gt($deadline);

        return view('acceptance.form', compact('acceptance', 'expired'));
    }

    public function submit(Request $request, string $token)
    {
        $acceptance = CandidateAcceptance::where('token', $token)->firstOrFail();

        if ($acceptance->responded_at) {
            return redirect()->back()->with('info', 'You have already submitted your response.');
        }

        $deadline = \App\Models\ElectionSetting::current()->acceptance_deadline_at;
        if ($deadline && now()->gt($deadline)) {
            return redirect()->back()->with('error', 'The acceptance deadline has passed.');
        }

        $request->validate([
            'accepted'      => 'required|in:1,0',
            'response_note' => 'nullable|string|max:1000',
        ]);

        $acceptance->update([
            'accepted'      => (bool) $request->accepted,
            'response_note' => $request->response_note,
            'responded_at'  => now(),
        ]);

        return view('acceptance.form', ['acceptance' => $acceptance, 'justSubmitted' => true]);
    }
}
