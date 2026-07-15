<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
    public const MAX_ATTEMPTS = 5;

    protected $fillable = ['user_id', 'token', 'attempts', 'expires_at', 'used_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    public function isLocked(): bool
    {
        return $this->attempts >= self::MAX_ATTEMPTS;
    }

    public static function generate(int $userId): self
    {
        // Invalidate any existing unused OTPs for this user
        static::where('user_id', $userId)->whereNull('used_at')->delete();

        return static::create([
            'user_id'    => $userId,
            'token'      => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
