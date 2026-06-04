<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            'Faculty of Science and Technology',
            'Faculty of Social Sciences',
            'School of Business',
            'School of Law',
            'School of Public Administration and Management',
        ];

        foreach ($faculties as $name) {
            Faculty::firstOrCreate(['name' => $name]);
        }
    }
}
