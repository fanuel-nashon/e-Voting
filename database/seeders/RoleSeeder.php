<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate so re-running the seeder is safe
        $admin         = Role::firstOrCreate(['name' => 'admin']);
        $voter         = Role::firstOrCreate(['name' => 'voter']);
        $electionAdmin = Role::firstOrCreate(['name' => 'election_admin']);

        // Assign permissions to roles
        // admin can manage everything
        $admin->syncPermissions(['manage_users', 'manage_election']);

        // election_admin can only manage election content
        $electionAdmin->syncPermissions(['manage_election']);

        // voter can only vote
        $voter->syncPermissions(['vote']);
    }
}
