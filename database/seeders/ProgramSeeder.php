<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Faculty of Engineering & Technology' => [
                'BSc Computer Science',
                'BSc Information Technology',
                'BSc Electrical Engineering',
                'BSc Civil Engineering',
            ],
            'Faculty of Science' => [
                'BSc Mathematics',
                'BSc Physics',
                'BSc Chemistry',
                'BSc Biology',
            ],
            'Faculty of Business & Economics' => [
                'BBA Business Administration',
                'BSc Accounting & Finance',
                'BSc Economics',
            ],
            'Faculty of Arts & Social Sciences' => [
                'BA Sociology',
                'BA Political Science',
                'BA Communication Studies',
            ],
            'Faculty of Medicine & Health Sciences' => [
                'MBChB Medicine & Surgery',
                'BSc Nursing',
                'BSc Public Health',
            ],
        ];

        foreach ($map as $facultyName => $programs) {
            $faculty = Faculty::where('name', $facultyName)->first();
            if (!$faculty) continue;

            foreach ($programs as $program) {
                Program::firstOrCreate(
                    ['name' => $program, 'faculty_id' => $faculty->id]
                );
            }
        }
    }
}
