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
            'Faculty of Science and Technology' => [
                'BSc ICT with Business',
                'BSc ICT with Management',
                'BSc Information Technology Systems',
            ],
            'Faculty of Social Sciences' => [
                'BA Sociology',
                'BA Political Science and Public Administration',
                'BA Development Studies',
                'BA Communication and Media Studies',
            ],
            'School of Business' => [
                'Bachelor of Accounting and Finance',
                'Bachelor of Entrepreneurship and Innovation Management',
                'Bachelor of Business Administration and Marketing',
            ],
            'School of Law' => [
                'LLB Law',
                'LLB Law with International Relations',
            ],
            'School of Public Administration and Management' => [
                'BA Public Administration',
                'BA Local Government Administration',
                'BA Human Resource Management',
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
