<?php

namespace App\Http\Controllers;

use App\Mail\CandidateResultMail;
use App\Mail\VoterResultsMail;
use App\Models\Candidate;
use App\Models\CandidateAcceptance;
use App\Models\ElectionSetting;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use App\Models\EmailLog;
use App\Models\VoteLog;
use App\Models\VoterRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ElectionAdminController extends Controller
{
    public function dashboard()
    {
        $election = ElectionSetting::current();

        $stats = [
            'total_votes'    => Vote::count(),
            'total_voters'   => Student::whereNotNull('user_id')->count(),
            'positions'      => Position::count(),
            'participation'  => Student::whereNotNull('user_id')->count() > 0
                ? round((Vote::distinct('student_id')->count() / Student::whereNotNull('user_id')->count()) * 100, 1)
                : 0,
        ];

        $acceptances = CandidateAcceptance::with(['candidate', 'position'])
            ->orderByDesc('created_at')
            ->get();

        $user = auth()->user();
        $pendingQuery = VoterRegistration::with(['program', 'faculty'])->where('status', 'pending');
        if ($user->hasRole('election_admin') && $user->faculty_id) {
            $pendingQuery->where('faculty_id', $user->faculty_id);
        }
        $pendingRegistrations = $pendingQuery->orderByDesc('created_at')->get();

        return view('election-admin.dashboard', compact('election', 'stats', 'acceptances', 'pendingRegistrations'));
    }

    // ── Timeline ──────────────────────────────────────────────────────────────
    public function saveTimeline(Request $request)
    {
        $request->validate([
            'voting_opens_at'        => 'required|date',
            'voting_closes_at'       => 'required|date|after:voting_opens_at',
            'acceptance_deadline_at' => 'nullable|date|after:voting_closes_at',
        ]);

        $election = ElectionSetting::current();
        $election->update($request->only('voting_opens_at', 'voting_closes_at', 'acceptance_deadline_at'));

        return response()->json(['success' => true, 'message' => 'Timeline saved successfully.']);
    }

    // ── Live log polling ──────────────────────────────────────────────────────
    public function pollLogs(Request $request)
    {
        $after = $request->integer('after', 0); // last log id seen by client

        $logs = VoteLog::where('id', '>', $after)
            ->orderBy('id')
            ->limit(50)
            ->get(['id', 'voter_hash', 'faculty_name', 'program_name', 'position_name', 'action', 'ip_prefix', 'created_at']);

        return response()->json([
            'logs'    => $logs,
            'last_id' => $logs->last()?->id ?? $after,
        ]);
    }

    // ── Release results (emails candidates) ───────────────────────────────────
    public function releaseResults()
    {
        $election = ElectionSetting::current();

        if (!$election->hasEnded()) {
            return response()->json(['success' => false, 'message' => 'Voting has not ended yet.'], 422);
        }

        if ($election->resultsReleased()) {
            return response()->json(['success' => false, 'message' => 'Results have already been released.'], 422);
        }

        // Calculate winners per position
        $positions = Position::with(['candidates.votes'])->get();

        DB::transaction(function () use ($positions, $election) {
            foreach ($positions as $position) {
                $candidates = $position->candidates;
                if ($candidates->isEmpty()) continue;

                $maxVotes  = $candidates->max(fn($c) => $c->votes->count());
                $winnerId  = $candidates->firstWhere(fn($c) => $c->votes->count() === $maxVotes)?->id;

                foreach ($candidates as $candidate) {
                    $won = ($candidate->id === $winnerId && $maxVotes > 0);

                    $acceptance = CandidateAcceptance::create([
                        'candidate_id'   => $candidate->id,
                        'position_id'    => $position->id,
                        'votes_received' => $candidate->votes->count(),
                        'won'            => $won,
                        'token'          => CandidateAcceptance::generateToken(),
                    ]);

                    if ($candidate->email) {
                        try {
                            Mail::to($candidate->email)->send(new CandidateResultMail($acceptance->load(['candidate', 'position'])));
                            $acceptance->update(['notification_sent_at' => now()]);
                            EmailLog::record('candidate_result', $candidate->email, 'sent');
                        } catch (\Exception $e) {
                            EmailLog::record('candidate_result', $candidate->email, 'failed', $e->getMessage());
                        }
                    }
                }
            }

            $election->update(['results_released_at' => now()]);
        });

        return response()->json(['success' => true, 'message' => 'Results released and candidates notified.']);
    }

    // ── Verify a candidate acceptance ─────────────────────────────────────────
    public function verifyAcceptance(CandidateAcceptance $acceptance)
    {
        $acceptance->update([
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Acceptance verified.']);
    }

    // ── Publish results to all voters ─────────────────────────────────────────
    public function publishResults()
    {
        $election = ElectionSetting::current();

        if (!$election->resultsReleased()) {
            return response()->json(['success' => false, 'message' => 'Results have not been released yet.'], 422);
        }

        // Build results array grouped by position
        $results = [];
        $acceptances = CandidateAcceptance::with(['candidate', 'position'])
            ->orderBy('position_id')
            ->orderByDesc('votes_received')
            ->get();

        foreach ($acceptances as $a) {
            $posName = $a->position->name;
            $results[$posName][] = [
                'name'  => $a->candidate->name,
                'votes' => $a->votes_received,
                'won'   => $a->won,
            ];
        }

        // Only email voters who have a real personal email address
        $voters  = \App\Models\User::role('voter')->get();
        $emailed = 0;
        foreach ($voters as $voter) {
            if (!$voter->personal_email) continue;
            try {
                Mail::to($voter->personal_email)->send(new VoterResultsMail($results, $election->title));
                EmailLog::record('voter_result', $voter->personal_email, 'sent');
                $emailed++;
            } catch (\Exception $e) {
                EmailLog::record('voter_result', $voter->personal_email, 'failed', $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => "Results emailed to {$emailed} voter(s) (out of {$voters->count()} total)."]);
    }

    // ── Email delivery logs ───────────────────────────────────────────────────
    public function pollEmailLogs(Request $request)
    {
        $query = EmailLog::orderByDesc('created_at')->limit(100);

        if ($request->query('status') === 'failed') {
            $query->where('status', 'failed');
        }

        $logs = $query->get(['id', 'type', 'recipient', 'status', 'failure_reason', 'created_at']);

        return response()->json(['logs' => $logs]);
    }

    // ── Live stats for dashboard widgets ──────────────────────────────────────
    public function pollStats()
    {
        $total   = \App\Models\Student::whereNotNull('user_id')->count();
        $voted   = Vote::distinct('student_id')->count('student_id');
        return response()->json([
            'total_votes'   => Vote::count(),
            'participation' => $total > 0 ? round($voted / $total * 100, 1) : 0,
            'voted_count'   => $voted,
        ]);
    }
}
