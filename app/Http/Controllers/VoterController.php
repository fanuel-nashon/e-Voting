<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Position;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoterController extends Controller
{
    public function dashboard()
    {
        $student  = auth()->user()->student;
        $election = ElectionSetting::current();

        if (!$student) {
            return view('voter.dashboard', [
                'noProfile' => true,
                'election'  => $election,
                'groups'    => [],
                'voted'     => [],
            ]);
        }

        // Positions this voter is eligible for
        $positions = Position::with(['candidates'])
            ->where(function ($q) use ($student) {
                $q->where('type', 'president')
                  ->orWhere(function ($q2) use ($student) {
                      $q2->whereIn('type', ['faculty_rep', 'senator'])
                         ->where('faculty_id', $student->faculty_id);
                  })
                  ->orWhere(function ($q2) use ($student) {
                      $q2->where('type', 'class_rep')
                         ->where('program_id', $student->program_id);
                  });
            })
            ->get();

        // Which positions has this voter already voted in?
        $voted = Vote::where('student_id', $student->id)
            ->pluck('position_id')
            ->toArray();

        // Group by type for display
        $typeLabels = [
            'president'   => 'University President',
            'faculty_rep' => 'Faculty Representative',
            'senator'     => 'Senator',
            'class_rep'   => 'Class Representative',
        ];

        $groups = $positions->groupBy('type')->map(function ($items, $type) use ($typeLabels) {
            return ['label' => $typeLabels[$type] ?? $type, 'positions' => $items];
        });

        return view('voter.dashboard', compact('student', 'election', 'groups', 'voted'));
    }

    public function review(Request $request)
    {
        $election = ElectionSetting::current();

        if (!$election->isOpen()) {
            return redirect()->route('voter.dashboard')->with('error', 'Voting is not currently open.');
        }

        $student = auth()->user()->student;
        if (!$student) return redirect()->route('voter.dashboard');

        $selections = $request->input('votes', []);  // [position_id => candidate_id]

        if (empty($selections)) {
            return redirect()->route('voter.dashboard')->with('error', 'Please select at least one candidate.');
        }

        // Validate each selection
        $reviewed = [];
        foreach ($selections as $positionId => $candidateId) {
            $candidate = Candidate::with('position')->find($candidateId);
            if (!$candidate || (int) $candidate->position_id !== (int) $positionId) continue;

            $reviewed[$positionId] = [
                'position_id'    => $positionId,
                'position_name'  => $candidate->position->name,
                'position_type'  => $candidate->position->type,
                'candidate_id'   => $candidateId,
                'candidate_name' => $candidate->name,
                'candidate_image'=> $candidate->image,
            ];
        }

        if (empty($reviewed)) {
            return redirect()->route('voter.dashboard')->with('error', 'Invalid selections. Please try again.');
        }

        // Store in session for confirmation step
        session(['ballot_review' => $reviewed]);

        return view('voter.confirm', compact('reviewed', 'election'));
    }

    public function confirm(Request $request)
    {
        $election = ElectionSetting::current();

        if (!$election->isOpen()) {
            return redirect()->route('voter.dashboard')->with('error', 'Voting is not currently open.');
        }

        $student = auth()->user()->student;
        if (!$student) return redirect()->route('voter.dashboard');

        $reviewed = session('ballot_review');
        if (!$reviewed) {
            return redirect()->route('voter.dashboard')->with('error', 'Session expired. Please re-select your candidates.');
        }

        try {
            DB::transaction(function () use ($student, $reviewed) {
                // Re-check for existing votes inside the transaction, with a row lock,
                // to close the race between the check and the insert below.
                $alreadyVoted = Vote::where('student_id', $student->id)
                    ->whereIn('position_id', array_keys($reviewed))
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyVoted) {
                    throw new \DomainException('already_voted');
                }

                foreach ($reviewed as $positionId => $item) {
                    Vote::create([
                        'student_id'   => $student->id,
                        'candidate_id' => $item['candidate_id'],
                        'position_id'  => $positionId,
                    ]);

                    VoteLog::record('vote_cast', [
                        'position_name' => $item['position_type'],
                        'metadata'      => ['position_label' => $item['position_name']],
                    ]);
                }
            });
        } catch (\DomainException | QueryException $e) {
            // QueryException here means the unique(student_id, position_id) constraint
            // caught a duplicate that slipped past the lockForUpdate check above.
            session()->forget('ballot_review');
            return redirect()->route('voter.dashboard')->with('error', 'You have already voted for one or more of these positions.');
        }

        session()->forget('ballot_review');

        return redirect()->route('voter.done');
    }

    public function done()
    {
        return view('voter.done', ['election' => ElectionSetting::current()]);
    }
}
