<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Simulates real-time voting for live demonstrations.
 *
 * Usage:
 *   php artisan demo:vote                         # all voters, normal pace
 *   php artisan demo:vote --speed=fast            # 0.1 s avg between voters
 *   php artisan demo:vote --speed=slow            # 2–5 s between voters
 *   php artisan demo:vote --reset                 # clear votes first, then run
 *   php artisan demo:vote --turnout=60            # only 60% of voters participate
 *   php artisan demo:vote --speed=fast --reset    # full fresh demo, quickly
 *
 * Open the Election Control Centre (/election) in your browser while this
 * runs to watch the live activity log fill up in real-time.
 */
class SimulateVoting extends Command
{
    protected $signature = 'demo:vote
        {--speed=normal  : Voting pace — fast (0.1 s), normal (0.5 s), slow (2.5 s)}
        {--turnout=78    : Target participation percentage, 1–100}
        {--reset         : Wipe all votes and vote_logs before simulating}
        {--voters=0      : Hard cap on number of voters to simulate (0 = use --turnout)}';

    protected $description = 'Simulate real-time voting for live system demonstrations';

    // Weighted probability table — candidates are sorted by id, first gets most votes
    private const WEIGHTS = [42, 28, 16, 9, 5];

    // Abstention rate per position (realistic ~12%)
    private const ABSTENTION_PCT = 12;

    // Simulated IP prefixes to rotate through
    private const IP_POOL = [
        '196.41.x.x', '196.42.x.x', '41.73.x.x', '41.222.x.x',
        '197.186.x.x', '41.188.x.x', '212.22.x.x', '196.43.x.x',
        '41.90.x.x',   '105.161.x.x', '197.250.x.x', '41.59.x.x',
    ];

    public function handle(): int
    {
        $this->printBanner();

        // ── 1. Election must be open ───────────────────────────────────────────
        $election = ElectionSetting::current();
        if (!$election->isOpen()) {
            $this->error('  Election is not open.');
            $this->line('  Run first:  php artisan db:seed --class=DemoSeeder');
            $this->line('');
            return self::FAILURE;
        }

        // ── 2. Optionally wipe existing votes ──────────────────────────────────
        if ($this->option('reset')) {
            $this->doReset();
        }

        // ── 3. Load voters and positions ───────────────────────────────────────
        $students = Student::with(['user', 'faculty', 'program'])
            ->whereNotNull('user_id')
            ->inRandomOrder()
            ->get();

        if ($students->isEmpty()) {
            $this->error('  No students found. Run: php artisan db:seed --class=DemoSeeder');
            return self::FAILURE;
        }

        $positions = Position::with('candidates')->get();

        if ($positions->isEmpty() || $positions->every(fn($p) => $p->candidates->isEmpty())) {
            $this->error('  No candidates found. Run the full seeder first.');
            return self::FAILURE;
        }

        // Decide how many voters will participate
        $hardCap    = (int) $this->option('voters');
        $turnoutPct = min(100, max(1, (int) $this->option('turnout')));
        $target     = (int) ceil($students->count() * $turnoutPct / 100);
        if ($hardCap > 0) {
            $target = min($target, $hardCap);
        }
        $voters = $students->take($target);

        $baseDelay = $this->resolveDelay();

        // ── 4. Print simulation plan ───────────────────────────────────────────
        $this->line(sprintf(
            '  Election    : <fg=yellow>%s</>  [<fg=green>OPEN</>]',
            $election->title ?: 'Untitled'
        ));
        $this->line(sprintf(
            '  Voters      : %d eligible  →  <fg=yellow>%d will vote</> (%d%%)',
            $students->count(), $voters->count(), $turnoutPct
        ));
        $this->line(sprintf(
            '  Speed       : <fg=yellow>%s</>  (~%.1f s avg per voter)',
            $this->option('speed'), $baseDelay
        ));
        $this->line(sprintf(
            '  Positions   : %d  |  Candidates: %d',
            $positions->count(),
            $positions->sum(fn($p) => $p->candidates->count())
        ));
        $this->line('');
        $this->line('  <fg=cyan>Watch live:</> open /election in your browser — votes appear every few seconds.');
        $this->line('');

        // ── 5. Run the simulation ──────────────────────────────────────────────
        $bar = $this->output->createProgressBar($voters->count());
        $bar->setFormat("  %current%/%max% [%bar%] %percent:3s%%  |  votes cast: <fg=yellow>%message%</>");
        $bar->setMessage('0');
        $bar->start();

        $totalVotes = 0;
        $totalVotersDone = 0;

        foreach ($voters as $student) {
            $eligible = $this->eligiblePositions($student, $positions);

            if ($eligible->isEmpty()) {
                $bar->advance();
                continue;
            }

            $votesThisRound = 0;

            DB::transaction(function () use ($student, $eligible, &$votesThisRound, &$totalVotes) {
                foreach ($eligible as $position) {
                    // Realistic abstention — voter skips this position
                    if (rand(1, 100) <= self::ABSTENTION_PCT) {
                        continue;
                    }

                    // Duplicate check (voter may already have voted in a previous run)
                    $alreadyVoted = Vote::where('student_id', $student->id)
                        ->where('position_id', $position->id)
                        ->exists();
                    if ($alreadyVoted) {
                        continue;
                    }

                    $candidate = $this->weightedPick($position->candidates);
                    if (!$candidate) {
                        continue;
                    }

                    Vote::create([
                        'student_id'   => $student->id,
                        'candidate_id' => $candidate->id,
                        'position_id'  => $position->id,
                    ]);

                    // VoteLog::record() relies on auth(), which is unavailable in CLI.
                    // Write directly so the live activity log on /election gets entries.
                    VoteLog::create([
                        'voter_hash'    => hash('sha256', $student->user_id . config('app.key')),
                        'faculty_name'  => $student->faculty?->name,
                        'program_name'  => $student->program?->name,
                        'position_name' => $position->type,
                        'action'        => 'vote_cast',
                        'ip_prefix'     => self::IP_POOL[array_rand(self::IP_POOL)],
                        'metadata'      => ['position_label' => $position->name, 'demo' => true],
                        'created_at'    => now(),
                    ]);

                    $votesThisRound++;
                    $totalVotes++;
                }
            });

            $totalVotersDone++;
            $bar->setMessage((string) $totalVotes);
            $bar->advance();

            if ($votesThisRound > 0) {
                // Jitter ±50% around base delay for a natural, human-like rhythm
                $sleep = $baseDelay * (0.5 + mt_rand(0, 100) / 100.0);
                usleep((int) ($sleep * 1_000_000));
            }
        }

        $bar->finish();
        $this->line('');
        $this->printSummary($totalVotes, $totalVotersDone, $students->count(), $election);

        return self::SUCCESS;
    }

    // ── Eligibility — mirrors VoterController::dashboard() ────────────────────
    private function eligiblePositions(Student $student, $positions)
    {
        return $positions->filter(function (Position $pos) use ($student) {
            return match ($pos->type) {
                'president'              => true,
                'faculty_rep', 'senator' => (int) $pos->faculty_id === (int) $student->faculty_id,
                'class_rep'              => (int) $pos->program_id === (int) $student->program_id,
                default                  => false,
            };
        });
    }

    // ── Weighted random candidate pick — creates realistic winner/runner-up ───
    private function weightedPick($candidates): ?Candidate
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        $sorted = $candidates->sortBy('id')->values();
        $pool   = [];

        foreach ($sorted as $i => $candidate) {
            $weight = self::WEIGHTS[$i] ?? 2;
            for ($w = 0; $w < $weight; $w++) {
                $pool[] = $candidate;
            }
        }

        return $pool[array_rand($pool)];
    }

    private function resolveDelay(): float
    {
        return match ($this->option('speed')) {
            'fast'  => 0.1,
            'slow'  => 2.5,
            default => 0.5,   // normal
        };
    }

    private function doReset(): void
    {
        $votes = Vote::count();
        $logs  = VoteLog::count();

        Vote::query()->delete();
        VoteLog::query()->delete();

        $this->warn("  Reset: {$votes} vote(s) and {$logs} log entry/entries cleared.");
        $this->line('');
    }

    private function printBanner(): void
    {
        $this->line('');
        $this->line('  +--------------------------------------------------+');
        $this->line('  |   e-Voting Demo Simulation — Mzumbe University   |');
        $this->line('  +--------------------------------------------------+');
        $this->line('');
    }

    private function printSummary(int $votes, int $voted, int $total, ElectionSetting $election): void
    {
        $rate    = $total > 0 ? round($voted / $total * 100, 1) : 0;
        $closes  = $election->voting_closes_at?->diffForHumans() ?? '—';

        $this->line('');
        $this->line('  +--------------------------------------------------+');
        $this->line("  |  Simulation complete                              |");
        $this->line('  +--------------------------------------------------+');
        $this->line(sprintf('  |  Voters simulated : %-5d / %-5d  (%s%%)', $voted, $total, $rate) . str_pad('|', max(1, 51 - strlen(sprintf('  |  Voters simulated : %d / %d  (%s%%)', $voted, $total, $rate)))));
        $this->line(sprintf('  |  Total votes cast : %-28d|', $votes));
        $this->line(sprintf('  |  Voting closes    : %-28s|', $closes));
        $this->line('  +--------------------------------------------------+');
        $this->line('');
        $this->line('  View results:');
        $this->line('    Live activity log  ->  <fg=cyan>/election</>');
        $this->line('    Full report        ->  <fg=cyan>/reports</>');
        $this->line('');
        $this->line('  To run again with fresh votes:');
        $this->line('    <fg=cyan>php artisan demo:vote --reset</>');
        $this->line('');
    }
}
