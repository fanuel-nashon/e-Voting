<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['type', 'recipient', 'status', 'failure_reason'];

    public static function record(string $type, string $recipient, string $status, ?string $failureReason = null): void
    {
        static::create([
            'type'           => $type,
            'recipient'      => $recipient,
            'status'         => $status,
            'failure_reason' => $failureReason,
        ]);
    }

    public static function labelFor(string $type): string
    {
        return match($type) {
            'voter_credentials' => 'Voter Credentials',
            'voter_otp'         => 'OTP',
            'candidate_result'  => 'Candidate Result',
            'voter_result'      => 'Voter Result',
            'password_reset'    => 'Password Reset',
            default             => $type,
        };
    }
}
