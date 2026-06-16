<?php

namespace App\Console\Commands;

use App\Models\CandidateAcceptance;
use App\Models\ElectionSetting;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetElection extends Command
{
    protected $signature = 'election:reset
                            {--candidates : Also delete all candidates}
                            {--yes : Skip confirmation prompt}';

    protected $description = 'Clear all election transaction data (votes, logs, acceptances, timeline) ready for a new test run';

    public function handle(): int
    {
        if (!$this->option('yes') && !$this->confirm('This will permanently delete all votes, vote logs, and candidate acceptances. Continue?')) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        DB::transaction(function () {
            $votes        = Vote::query()->count();
            $logs         = VoteLog::query()->count();
            $acceptances  = CandidateAcceptance::query()->count();

            Vote::query()->delete();
            VoteLog::query()->delete();
            CandidateAcceptance::query()->delete();

            // Reset election timeline and release flags
            ElectionSetting::query()->update([
                'voting_opens_at'       => null,
                'voting_closes_at'      => null,
                'results_released_at'   => null,
                'acceptance_deadline_at' => null,
            ]);

            $this->info("Deleted {$votes} vote(s), {$logs} log entry(ies), {$acceptances} acceptance record(s).");
            $this->info('Election timeline cleared.');

            if ($this->option('candidates')) {
                $candidates = \App\Models\Candidate::query()->count();
                \App\Models\Candidate::query()->delete();
                $this->info("Deleted {$candidates} candidate(s).");
            }
        });

        $this->newLine();
        $this->components->info('Election reset complete. You can now configure a new timeline and run a fresh election.');

        return self::SUCCESS;
    }
}
