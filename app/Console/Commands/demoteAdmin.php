<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class demoteAdmin extends Command
{

    protected $signature = 'user:demote-admin {username}';
    protected $description = 'Demote an admin user to a regular user role';

    public function handle()
    {
        $username = $this->argument('username');

        $user = User::where('name', $username)->first();

        if (!$user) {
            $this->error("User with username '{$username}' was not found.");
            return 1;
        }

        if ($user->role == 0) {
            $this->info("User '{$username}' is already a regular user.");
            return 0;
        }

        $user->role = 0;
        $user->save();

        $this->info("Success! User '{$username}' has been demoted to a regular user.");

        return 0;
    }

}

?>
