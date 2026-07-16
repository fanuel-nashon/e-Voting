<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoterRegistration extends Model
{
    protected $fillable = [
        'name', 'email', 'reg_number', 'reg_year',
        'program_id', 'faculty_id', 'photo',
        'status', 'processed_by', 'processed_at', 'rejection_reason',
    ];

    public const EMAIL_DOMAIN = 'mustudent.ac.tz';

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

    public static function nameSlug(string $name): string
    {
        $parts     = explode(' ', strtolower(trim($name)));
        $firstName = preg_replace('/[^a-z]/', '', $parts[0] ?? 'student');
        $lastName  = preg_replace('/[^a-z]/', '', $parts[1] ?? '');

        return $lastName ? "{$firstName}.{$lastName}" : $firstName;
    }

    public static function expectedEmailBase(string $name, int $year): string
    {
        return self::nameSlug($name) . substr((string) $year, -2);
    }

    public static function buildEmail(string $name, int $year): string
    {
        $base  = self::expectedEmailBase($name, $year);
        $email = "{$base}@" . self::EMAIL_DOMAIN;

        // Ensure uniqueness
        $i = 2;
        while (User::where('email', $email)->exists() || VoterRegistration::where('email', $email)->exists()) {
            $email = "{$base}.{$i}@" . self::EMAIL_DOMAIN;
            $i++;
        }

        return $email;
    }

    /**
     * Whether $email matches the required firstname.lastname+YY@domain
     * pattern derived from the voter's own name and enrolment year
     * (optionally suffixed with .2, .3, ... for name/year collisions).
     */
    public static function emailMatchesPattern(string $name, int $year, string $email): bool
    {
        $base    = preg_quote(self::expectedEmailBase($name, $year), '/');
        $domain  = preg_quote(self::EMAIL_DOMAIN, '/');
        $pattern = "/^{$base}(\\.\\d+)?@{$domain}$/i";

        return (bool) preg_match($pattern, trim($email));
    }
}
