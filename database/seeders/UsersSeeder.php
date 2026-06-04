<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name'     => 'admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('Password_123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Election Admin',
                'email'    => 'electionadmin@gmail.com',
                'password' => Hash::make('Password_123'),
                'role'     => 'election_admin',
            ],
        ];

        foreach ($accounts as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = \App\Models\User::where('email', $data['email'])->first();

            if ($user) {
                $user->update($data);
            } else {
                $user = \App\Models\User::create($data);
            }

            $user->syncRoles([$role]);
        }
    }
}
