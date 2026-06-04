<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. President — one university-wide position
        Position::firstOrCreate([
            'name'       => 'University Student President',
            'type'       => 'president',
            'faculty_id' => null,
            'program_id' => null,
        ]);

        // 2. Faculty Representatives — one per faculty
        foreach (Faculty::all() as $faculty) {
            Position::firstOrCreate([
                'name'       => $faculty->name . ' Representative',
                'type'       => 'faculty_rep',
                'faculty_id' => $faculty->id,
                'program_id' => null,
            ]);

            // 3. Senators — one per faculty
            Position::firstOrCreate([
                'name'       => $faculty->name . ' Senator',
                'type'       => 'senator',
                'faculty_id' => $faculty->id,
                'program_id' => null,
            ]);
        }

        // 4. Class Representatives — one per program
        foreach (Program::with('faculty')->get() as $program) {
            Position::firstOrCreate([
                'name'       => $program->name . ' Class Rep',
                'type'       => 'class_rep',
                'faculty_id' => $program->faculty_id,
                'program_id' => $program->id,
            ]);
        }
    }
}
