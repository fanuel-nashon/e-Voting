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
        $userData=[
            'name'=>'admin',
            'email'=>'admin@gmail.com',
            'password'=>Hash::make('Password_123')
        ];

        $user = \App\Models\User::where('email', $userData['email'])->first();
        if($user){
            $user->update($userData);
            $user->assignRole('admin');
            $user->save();
            return;
        }

        \App\Models\User::firstOrCreate($userData);
    }
}
