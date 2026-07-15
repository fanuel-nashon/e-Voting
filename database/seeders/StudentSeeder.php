<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Models\VoterRegistration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $voterRole = Role::firstOrCreate(['name' => 'voter']);

        $firstNames = [
            'Amina','Brian','Cynthia','David','Esther','Frank','Grace','Hassan',
            'Irene','James','Khadija','Leonard','Mary','Nassim','Olivia','Patrick',
            'Queen','Robert','Sarah','Thomas','Ujima','Victor','Winifred','Xavier',
            'Yasmine','Zuberi',
        ];
        $lastNames = [
            'Kimani','Ochieng','Mutua','Wanjiku','Mwangi','Otieno','Njoroge',
            'Kariuki','Kamau','Oduya','Ndiaye','Bello','Diallo','Kofi','Mensah',
        ];

        $counter = 1;

        foreach (Program::with('faculty')->get() as $program) {
            for ($i = 1; $i <= 6; $i++) {
                $first = $firstNames[array_rand($firstNames)];
                $last  = $lastNames[array_rand($lastNames)];
                $name  = "$first $last";
                $regNo = 'STD/' . str_pad($counter, 4, '0', STR_PAD_LEFT) . '/2024';
                $email = VoterRegistration::buildEmail($name, 2024);

                // Create user
                $user = User::firstOrCreate(
                    ['email' => $email],
                    ['name' => $name, 'password' => Hash::make('student123')]
                );
                $user->syncRoles([$voterRole]);

                // Create student record
                Student::firstOrCreate(
                    ['reg_no' => $regNo],
                    [
                        'name'       => $name,
                        'program_id' => $program->id,
                        'faculty_id' => $program->faculty_id,
                        'user_id'    => $user->id,
                    ]
                );

                $counter++;
            }
        }
    }
}
