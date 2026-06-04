<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            'Faculty of Engineering & Technology',
            'Faculty of Science',
            'Faculty of Business & Economics',
            'Faculty of Arts & Social Sciences',
            'Faculty of Medicine & Health Sciences',
        ];

        foreach ($faculties as $name) {
            Faculty::firstOrCreate(['name' => $name]);
        }
    }
}
