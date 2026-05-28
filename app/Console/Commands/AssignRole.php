<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRole extends Command
{
    protected $signature = 'role:assign {email} {role}';

    protected $description = 'Assign a role to a user by email. Available roles: admin, election_admin, voter';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role  = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return self::FAILURE;
        }

        if (!Role::where('name', $role)->exists()) {
            $available = Role::pluck('name')->join(', ');
            $this->error("Role '{$role}' does not exist. Available: {$available}");
            return self::FAILURE;
        }

        $user->syncRoles([$role]);

        $this->info("Assigned role '{$role}' to {$user->name} ({$user->email})");
        return self::SUCCESS;
    }
}
