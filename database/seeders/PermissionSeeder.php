<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manage_users=Permission::create(['name'=>'manage_users']);
        $vote=Permission::create(['name'=>'vote']);
        $manage_election=Permission::create(['name'=>'manage_election']);
    }
}
