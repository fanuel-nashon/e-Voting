<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ElectionSetting extends Model
{
    protected $fillable = [
        'title', 'voting_opens_at', 'voting_closes_at',
        'results_released_at', 'acceptance_deadline_at',
    ];

    protected $casts = [
        'voting_opens_at'       => 'datetime',
        'voting_closes_at'      => 'datetime',
        'results_released_at'   => 'datetime',
        'acceptance_deadline_at'=> 'datetime',
    ];

    // Always work with the first (singleton) row
    public static function current(): self
    {
        return static::firstOrCreate([], ['title' => 'Student Union Election']);
    }

    public function isOpen(): bool
    {
        if (!$this->voting_opens_at || !$this->voting_closes_at) return false;
        $now = now();
        return $now->gte($this->voting_opens_at) && $now->lte($this->voting_closes_at);
    }

    public function hasEnded(): bool
    {
        return $this->voting_closes_at && now()->gt($this->voting_closes_at);
    }

    public function hasStarted(): bool
    {
        return $this->voting_opens_at && now()->gte($this->voting_opens_at);
    }

    public function resultsReleased(): bool
    {
        return !is_null($this->results_released_at);
    }
}
