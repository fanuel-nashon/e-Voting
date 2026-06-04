<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CandidateAcceptance extends Model
{
    protected $fillable = [
        'candidate_id', 'position_id', 'votes_received', 'won',
        'token', 'notification_sent_at', 'responded_at',
        'accepted', 'response_note', 'verified_at', 'verified_by',
    ];

    protected $casts = [
        'won'                   => 'boolean',
        'accepted'              => 'boolean',
        'notification_sent_at'  => 'datetime',
        'responded_at'          => 'datetime',
        'verified_at'           => 'datetime',
    ];

    public function candidate()  { return $this->belongsTo(Candidate::class); }
    public function position()   { return $this->belongsTo(Position::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }

    public static function generateToken(): string
    {
        return hash('sha256', Str::uuid() . config('app.key') . now()->timestamp);
    }

    public function statusLabel(): string
    {
        if (!$this->responded_at) return 'Awaiting Response';
        if ($this->accepted)      return 'Accepted';
        return 'Declined';
    }
}
