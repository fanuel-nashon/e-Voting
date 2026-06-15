<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateAcceptance;
use App\Models\ElectionSetting;
use App\Models\Faculty;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $election = ElectionSetting::current();

        // ── Overall stats ─────────────────────────────────────────────────────
        $totalVoters     = Student::whereNotNull('user_id')->count();
        $totalVoted      = Vote::distinct('student_id')->count('student_id');
        $totalVotes      = Vote::count();
        $totalCandidates = Candidate::count();
        $totalPositions  = Position::count();
        $participation   = $totalVoters > 0 ? round($totalVoted / $totalVoters * 100, 1) : 0;
        $notVoted        = $totalVoters - $totalVoted;

        // ── Positions with full candidate breakdown ───────────────────────────
        $positions = Position::with([
            'faculty',
            'program',
            'candidates' => fn($q) => $q->withCount('votes'),
        ])->get()->map(function ($pos) {
            $sorted = $pos->candidates->sortByDesc('votes_count');
            $total  = $sorted->sum('votes_count');
            $winner = $sorted->first();
            return [
                'id'          => $pos->id,
                'name'        => $pos->name,
                'type'        => $pos->type,
                'faculty'     => $pos->faculty?->name,
                'program'     => $pos->program?->name,
                'total_votes' => $total,
                'candidates'  => $sorted->map(fn($c) => [
                    'id'         => $c->id,
                    'name'       => $c->name,
                    'image'      => $c->image,
                    'votes'      => $c->votes_count,
                    'percentage' => $total > 0 ? round($c->votes_count / $total * 100, 1) : 0,
                    'is_winner'  => $winner && $c->id === $winner->id && $c->votes_count > 0,
                ])->values(),
            ];
        })->values();

        // ── Faculty breakdown ─────────────────────────────────────────────────
        $faculties = Faculty::orderBy('name')->get()->map(function ($faculty) {
            $totalEligible = Student::whereNotNull('user_id')->where('faculty_id', $faculty->id)->count();
            $studentIds    = Student::where('faculty_id', $faculty->id)->pluck('id');
            $voted         = $studentIds->isNotEmpty()
                ? Vote::whereIn('student_id', $studentIds)->distinct('student_id')->count('student_id')
                : 0;
            $rate = $totalEligible > 0 ? round($voted / $totalEligible * 100, 1) : 0;

            $facultyPositions = Position::where('faculty_id', $faculty->id)
                ->with(['candidates' => fn($q) => $q->withCount('votes')])
                ->get()
                ->map(fn($p) => [
                    'name'       => $p->name,
                    'type'       => $p->type,
                    'candidates' => $p->candidates->sortByDesc('votes_count')->map(fn($c) => [
                        'name'  => $c->name,
                        'votes' => $c->votes_count,
                    ])->values(),
                ])->values();

            return [
                'id'            => $faculty->id,
                'name'          => $faculty->name,
                'total_voters'  => $totalEligible,
                'voted_count'   => $voted,
                'not_voted'     => $totalEligible - $voted,
                'participation' => $rate,
                'positions'     => $facultyPositions,
            ];
        })->values();

        // ── Candidates comprehensive ──────────────────────────────────────────
        $positionTotals = Vote::select('position_id', DB::raw('count(*) as total'))
            ->groupBy('position_id')
            ->pluck('total', 'position_id');

        $acceptanceMap = CandidateAcceptance::all()->keyBy('candidate_id');

        $candidates = Candidate::with(['position.faculty', 'position.program'])
            ->withCount('votes')
            ->get()
            ->map(function ($c) use ($positionTotals, $acceptanceMap) {
                $posTotal   = $positionTotals[$c->position_id] ?? 0;
                $acceptance = $acceptanceMap[$c->id] ?? null;
                return [
                    'id'             => $c->id,
                    'name'           => $c->name,
                    'image'          => $c->image,
                    'position'       => $c->position?->name,
                    'position_type'  => $c->position?->type,
                    'affiliation'    => $c->position?->faculty?->name ?? $c->position?->program?->name,
                    'votes'          => $c->votes_count,
                    'position_total' => (int) $posTotal,
                    'percentage'     => $posTotal > 0 ? round($c->votes_count / $posTotal * 100, 1) : 0,
                    'won'            => $acceptance?->won,
                    'accepted'       => $acceptance?->accepted,
                    'results_out'    => $acceptance !== null,
                ];
            })->sortByDesc('votes')->values();

        // ── Vote timeline (VoteLog by hour) ───────────────────────────────────
        $votingActivity = VoteLog::where('action', 'vote_cast')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn($r) => ['hour' => $r->hour, 'count' => (int) $r->count])
            ->values();

        // ── Votes by faculty (from vote log — anonymised) ─────────────────────
        $votesByFaculty = VoteLog::where('action', 'vote_cast')
            ->whereNotNull('faculty_name')
            ->select('faculty_name', DB::raw('count(*) as count'))
            ->groupBy('faculty_name')
            ->orderByDesc('count')
            ->get();

        // ── Voters who voted per program ──────────────────────────────────────
        $votesByProgram = DB::table('votes')
            ->join('students', 'votes.student_id', '=', 'students.id')
            ->join('programs', 'students.program_id', '=', 'programs.id')
            ->selectRaw('programs.name as program, COUNT(DISTINCT votes.student_id) as voted_count')
            ->groupBy('programs.name')
            ->orderByDesc('voted_count')
            ->get();

        return view('reports.index', compact(
            'election',
            'totalVoters', 'totalVoted', 'totalVotes', 'notVoted',
            'totalCandidates', 'totalPositions', 'participation',
            'positions', 'faculties', 'candidates',
            'votingActivity', 'votesByFaculty', 'votesByProgram'
        ));
    }

    // ── CSV Export ────────────────────────────────────────────────────────────
    public function exportCsv(Request $request)
    {
        $type     = $request->query('type', 'overall');
        $filename = 'voting-report-' . $type . '-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        $callback = match ($type) {
            'candidates' => fn() => $this->candidatesCsv(),
            'faculties'  => fn() => $this->facultiesCsv(),
            'positions'  => fn() => $this->positionsCsv(),
            default      => fn() => $this->overallCsv(),
        };

        return response()->stream($callback, 200, $headers);
    }

    private function overallCsv(): void
    {
        $out         = fopen('php://output', 'w');
        $totalVoters = Student::whereNotNull('user_id')->count();
        $totalVoted  = Vote::distinct('student_id')->count('student_id');
        $totalVotes  = Vote::count();
        $rate        = $totalVoters > 0 ? round($totalVoted / $totalVoters * 100, 1) : 0;

        fputcsv($out, ['Overall Election Report — Generated: ' . now()->format('d M Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Total Registered Voters', $totalVoters]);
        fputcsv($out, ['Total Voters Who Voted',  $totalVoted]);
        fputcsv($out, ['Did Not Vote',             $totalVoters - $totalVoted]);
        fputcsv($out, ['Total Votes Cast',         $totalVotes]);
        fputcsv($out, ['Participation Rate',        $rate . '%']);
        fputcsv($out, []);

        fputcsv($out, ['Results by Position']);
        fputcsv($out, ['Position', 'Type', 'Candidate', 'Votes', 'Vote %', 'Status']);

        Position::with(['candidates' => fn($q) => $q->withCount('votes')])->get()->each(function ($pos) use ($out) {
            $total  = $pos->candidates->sum('votes_count');
            $sorted = $pos->candidates->sortByDesc('votes_count');
            $winner = $sorted->first();
            $sorted->each(function ($c) use ($out, $pos, $total, $winner) {
                $pct    = $total > 0 ? round($c->votes_count / $total * 100, 1) : 0;
                $status = ($winner && $c->id === $winner->id && $c->votes_count > 0) ? 'Winner' : 'Runner-up';
                fputcsv($out, [$pos->name, $pos->type, $c->name, $c->votes_count, $pct . '%', $status]);
            });
        });

        fclose($out);
    }

    private function candidatesCsv(): void
    {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Candidate Report — Generated: ' . now()->format('d M Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, ['Candidate', 'Position', 'Type', 'Faculty / Program', 'Votes', 'Vote %', 'Position Total Votes', 'Status']);

        $positionTotals = Vote::select('position_id', DB::raw('count(*) as total'))
            ->groupBy('position_id')
            ->pluck('total', 'position_id');

        $acceptanceMap = CandidateAcceptance::all()->keyBy('candidate_id');

        Candidate::with(['position.faculty', 'position.program'])
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get()
            ->each(function ($c) use ($out, $positionTotals, $acceptanceMap) {
                $posTotal   = $positionTotals[$c->position_id] ?? 0;
                $acceptance = $acceptanceMap[$c->id] ?? null;
                $affil      = $c->position?->faculty?->name ?? $c->position?->program?->name ?? '—';
                $pct        = $posTotal > 0 ? round($c->votes_count / $posTotal * 100, 1) : 0;
                $status     = $acceptance ? ($acceptance->won ? 'Winner' : 'Runner-up') : 'Results Pending';
                fputcsv($out, [
                    $c->name,
                    $c->position?->name ?? '—',
                    $c->position?->type ?? '—',
                    $affil,
                    $c->votes_count,
                    $pct . '%',
                    $posTotal,
                    $status,
                ]);
            });

        fclose($out);
    }

    private function facultiesCsv(): void
    {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Faculty Participation Report — Generated: ' . now()->format('d M Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, ['Faculty', 'Registered Voters', 'Voted', 'Did Not Vote', 'Participation Rate']);

        Faculty::orderBy('name')->get()->each(function ($f) use ($out) {
            $total  = Student::whereNotNull('user_id')->where('faculty_id', $f->id)->count();
            $ids    = Student::where('faculty_id', $f->id)->pluck('id');
            $voted  = $ids->isNotEmpty()
                ? Vote::whereIn('student_id', $ids)->distinct('student_id')->count('student_id')
                : 0;
            $rate   = $total > 0 ? round($voted / $total * 100, 1) : 0;
            fputcsv($out, [$f->name, $total, $voted, $total - $voted, $rate . '%']);
        });

        fclose($out);
    }

    private function positionsCsv(): void
    {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Position Report — Generated: ' . now()->format('d M Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, ['Position', 'Type', 'Faculty / Program', 'Candidate', 'Votes', 'Vote %', 'Status']);

        Position::with(['candidates' => fn($q) => $q->withCount('votes'), 'faculty', 'program'])
            ->get()
            ->each(function ($pos) use ($out) {
                $total  = $pos->candidates->sum('votes_count');
                $affil  = $pos->faculty?->name ?? $pos->program?->name ?? '—';
                $sorted = $pos->candidates->sortByDesc('votes_count');
                $winner = $sorted->first();
                $sorted->each(function ($c) use ($out, $pos, $total, $affil, $winner) {
                    $pct    = $total > 0 ? round($c->votes_count / $total * 100, 1) : 0;
                    $status = ($winner && $c->id === $winner->id && $c->votes_count > 0) ? 'Winner' : 'Runner-up';
                    fputcsv($out, [$pos->name, $pos->type, $affil, $c->name, $c->votes_count, $pct . '%', $status]);
                });
            });

        fclose($out);
    }
}
