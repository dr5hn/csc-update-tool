<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AssignAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'jaiswalaakash789@gmail.com')
            ->update(['is_admin' => true]);
            
        $this->command->info('Admin privileges assigned successfully!');
    }
}