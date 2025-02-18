<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyAdminUser extends Command
{
    protected $signature = 'admin:verify {email?}';
    protected $description = 'Verify admin status for a user';

    public function handle()
    {
        $email = $this->argument('email') ?? 'jaiswalaakash789@gmail.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        if ($user->is_admin) {
            $this->info("✓ User {$email} is confirmed as admin");
            return 0;
        }

        $this->error("✗ User {$email} is not an admin");
        return 1;
    }
}