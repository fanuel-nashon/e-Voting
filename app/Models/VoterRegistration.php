<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoterRegistration extends Model
{
    protected $fillable = [
        'name', 'email', 'personal_email', 'reg_number', 'reg_year',
        'program_id', 'faculty_id', 'photo',
        'status', 'processed_by', 'processed_at', 'rejection_reason',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'reg_year'     => 'integer',
    ];

    public function program()     { return $this->belongsTo(Program::class); }
    public function faculty()     { return $this->belongsTo(Faculty::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }

    public static function extractYear(string $regNumber): int
    {
        preg_match('/\d{4}/', $regNumber, $m);
        return (int) ($m[0] ?? date('Y'));
    }

    public static function buildEmail(string $name, int $year): string
    {
        $parts     = explode(' ', strtolower(trim($name)));
        $firstName = preg_replace('/[^a-z]/', '', $parts[0] ?? 'student');
        $lastName  = preg_replace('/[^a-z]/', '', $parts[1] ?? '');
        $base      = $lastName ? "{$firstName}.{$lastName}" : $firstName;

        $email = "{$base}.{$year}@mzumbeuniversity.com";

        // Ensure uniqueness
        $i = 2;
        while (User::where('email', $email)->exists() || VoterRegistration::where('email', $email)->exists()) {
            $email = "{$base}.{$year}.{$i}@mzumbeuniversity.com";
            $i++;
        }

        return $email;
    }
}
