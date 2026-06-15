<?php

namespace Database\Seeders;

use App\Models\ElectionSetting;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Sets up the demo environment for real-time voting demonstrations.
 *
 * Usage:
 *   php artisan db:seed --class=DemoSeeder
 *   php artisan demo:vote --speed=normal
 *
 * Creates 80 additional demo voter accounts spread across all programmes,
 * then opens the election window (now → +4 hours) so voting can proceed.
 * Existing seeded voters are preserved — this seeder is idempotent.
 */
class DemoSeeder extends Seeder
{
    private const VOTER_COUNT = 80;
    private const DEMO_YEAR   = '2024';
    private const EMAIL_DOMAIN = 'mzumbeuniversity.com';

    private const FIRST_NAMES = [
        'Amina','Baraka','Celestine','Dalila','Emmanuel','Fatuma','Grace',
        'Hassan','Irene','John','Kezia','Lulu','Makame','Neema','Omar',
        'Pendo','Rashid','Salome','Tatu','Upendo','Victor','Wema','Yusuf',
        'Zaituni','Abel','Beatrice','Charles','Diana','Ernest','Florence',
        'George','Hidaya','Innocent','Jane','Kelvin','Leila','Morris',
        'Naima','Oliver','Pauline','Rehema','Samuel','Theresia','Uhuru',
        'Veronica','Wilson','Yohana','Zena','Abdi','Brenda','Collins',
        'Debora','Elias','Faith','Godlisten','Helena','Isaac','Judith',
        'Khamis','Lillian','Mussa','Nadia','Patrick','Queen','Stella',
        'Thomas','Uswege','Vivian','William','Zahra','Antony','Bupe',
        'Conrad','Doto','Evelyn','Fredrick','Gladness','Herbert','Imani',
        'Joyce','Kabula','Latifa','Mungu','Neha','Petro','Ramadhan',
    ];

    private const LAST_NAMES = [
        'Juma','Mwangi','Osei','Hassan','Nyandoto','Bakari','Muthoni',
        'Ally','Mwamba','Mwita','Banda','Mwanga','Haji','Kilian','Masoud',
        'Mrisho','Ibrahim','Nyangasa','Issa','Mjomba','Nkya','Waweru',
        'Suleiman','Dube','Mwinyi','Kombo','Mosha','Lyimo','Mkwawa',
        'Mallya','Mwakyusa','Msema','Mwalimu','Chande','Kibasa','Kavishe',
        'Kimaro','Mfaume','Massawe','Swai','Mushi','Mtui','Mwanri',
        'Samwel','Mrema','Mgaya','Njau','Mmbaga','Mcharo','Mlay',
        'Chale','Minja','Mkinga','Saidi','Mkongo','Mwaka','Makundi',
    ];

    public function run(): void
    {
        $this->command->line('');
        $this->command->line('  <fg=white;bg=blue> Demo Environment Setup </>');
        $this->command->line('');

        // ── Open the election window ───────────────────────────────────────────
        $election = ElectionSetting::current();
        $election->update([
            'title'            => $election->title ?: 'MUSU Student Union Elections 2024',
            'voting_opens_at'  => now()->subMinutes(2),
            'voting_closes_at' => now()->addHours(4),
        ]);
        $this->command->info('  [1/2] Election window: OPEN now, closes in 4 hours.');

        // ── Guard: positions and candidates must already exist ─────────────────
        if (\App\Models\Position::count() === 0 || \App\Models\Candidate::count() === 0) {
            $this->command->error('  No positions/candidates found. Run the full seeder first:');
            $this->command->line('         php artisan migrate:fresh --seed');
            return;
        }

        $programs = Program::with('faculty')->get();
        if ($programs->isEmpty()) {
            $this->command->error('  No programmes found. Run the full seeder first.');
            return;
        }

        // ── Create demo voters ─────────────────────────────────────────────────
        $voterRole = Role::firstOrCreate(['name' => 'voter', 'guard_name' => 'web']);
        $created   = 0;
        $skipped   = 0;

        for ($i = 1; $i <= self::VOTER_COUNT; $i++) {
            $email = "demo.voter.{$i}@" . self::EMAIL_DOMAIN;

            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            $program = $programs[($i - 1) % $programs->count()];
            $name    = $this->makeName($i);
            $regNo   = sprintf(
                'DEMO/%s/%s/%03d',
                strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $program->name), 0, 4)),
                self::DEMO_YEAR,
                $i
            );

            $user = User::create([
                'name'           => $name,
                'email'          => $email,
                'personal_email' => $email,
                'password'       => Hash::make('demo1234'),
                'faculty_id'     => $program->faculty_id,
            ]);
            $user->assignRole($voterRole);

            Student::create([
                'reg_no'     => $regNo,
                'name'       => $name,
                'program_id' => $program->id,
                'faculty_id' => $program->faculty_id,
                'user_id'    => $user->id,
            ]);

            $created++;
        }

        $total = Student::whereNotNull('user_id')->count();
        $this->command->info("  [2/2] Demo voters: {$created} created, {$skipped} already existed. ({$total} total voters in system)");
        $this->command->line('');
        $this->command->line('  Ready. Run the simulation:');
        $this->command->line('    <fg=cyan>php artisan demo:vote</>                     normal pace (~0.5 s/voter)');
        $this->command->line('    <fg=cyan>php artisan demo:vote --speed=fast</>        fast pace  (~0.1 s/voter)');
        $this->command->line('    <fg=cyan>php artisan demo:vote --speed=fast --reset</> reset first, then run fast');
        $this->command->line('');
    }

    private function makeName(int $i): string
    {
        $first = self::FIRST_NAMES[($i - 1) % count(self::FIRST_NAMES)];
        $last  = self::LAST_NAMES[($i - 1) % count(self::LAST_NAMES)];
        return "{$first} {$last}";
    }
}
