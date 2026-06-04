<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            FacultySeeder::class,
            ProgramSeeder::class,
            PositionSeeder::class,
            StudentSeeder::class,   // creates voter users + student profiles
            CandidateSeeder::class,
        ]);
    }
}
