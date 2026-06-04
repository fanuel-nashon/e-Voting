<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'voter_hash', 'faculty_name', 'program_name',
        'position_name', 'action', 'ip_prefix', 'metadata',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    public static function record(string $action, array $data = []): void
    {
        $user    = auth()->user();
        $student = $user?->student;

        static::create([
            'voter_hash'    => hash('sha256', ($user?->id ?? 'anon') . config('app.key')),
            'faculty_name'  => $student?->faculty?->name,
            'program_name'  => $student?->program?->name,
            'position_name' => $data['position_name'] ?? '',
            'action'        => $action,
            'ip_prefix'     => static::ipPrefix(request()->ip()),
            'metadata'      => $data['metadata'] ?? null,
            'created_at'    => now(),
        ]);
    }

    private static function ipPrefix(?string $ip): ?string
    {
        if (!$ip) return null;
        $parts = explode('.', $ip);
        return count($parts) >= 2 ? $parts[0] . '.' . $parts[1] . '.x.x' : $ip;
    }
}
