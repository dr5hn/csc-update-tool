<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeographicalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Seeding geographical data...');

        // Check if data already exists
        $hasData = $this->hasExistingData();

        if ($hasData) {
            $this->command->info('✓ Geographical data already exists, skipping...');
            return;
        }

        try {
            // Use our sync command to populate data
            $exitCode = Artisan::call('geo:sync', ['--force' => true]);

            if ($exitCode === 0) {
                $this->command->info('✅ Geographical data seeded successfully!');
            } else {
                $this->command->error('❌ Failed to seed geographical data');
                Log::error('Geographical data seeding failed via Artisan command');
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Error during geographical data seeding: ' . $e->getMessage());
            Log::error('Geographical data seeding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function hasExistingData(): bool
    {
        try {
            $counts = [
                'countries' => DB::table('countries')->count(),
                'states' => DB::table('states')->count(),
                'cities' => DB::table('cities')->count(),
            ];

            return $counts['countries'] > 0 || $counts['states'] > 0 || $counts['cities'] > 0;

        } catch (\Exception $e) {
            // If tables don't exist yet, return false
            return false;
        }
    }
}
