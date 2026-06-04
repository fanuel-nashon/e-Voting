<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Position;

class CandidateSeeder extends Seeder
{
    private array $firstNames = [
        'Ali','Beatrice','Carlos','Diana','Emmanuel','Fatuma','George','Halima',
        'Ivan','Janet','Kevin','Layla','Michael','Nadia','Omar','Priya',
    ];
    private array $lastNames = [
        'Abubakar','Chirwa','Dlamini','Eze','Fadel','Gomes','Hamisi',
        'Ibrahim','Juma','Kamau','Lumumba','Mahdi','Nkosi','Osei',
    ];

    public function run(): void
    {
        foreach (Position::all() as $position) {
            // 2 or 3 candidates per position
            $count = ($position->type === 'president') ? 3 : 2;

            for ($i = 0; $i < $count; $i++) {
                $name = $this->firstNames[array_rand($this->firstNames)]
                      . ' '
                      . $this->lastNames[array_rand($this->lastNames)];

                Candidate::create([
                    'name'        => $name,
                    'image'       => null,
                    'position_id' => $position->id,
                ]);
            }
        }
    }
}
