<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class makeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {username}';

    protected $description = 'Promote a regular user to an admin role';

    public function handle()
    {
        $username = $this->argument('username');

        $user = User::where('name', $username)->first();

        if (!$user) {
            $this->error("User with username '{$username}' was not found.");
            return 1;
        }

        if ($user->role == 1) {
            $this->info("User '{$username}' is already an admin.");
            return 0;
        }

        $user->role = 1;
        $user->save();

        $this->info("Success! User '{$username}' has been promoted to an admin.");

        return 0;
    }
}

