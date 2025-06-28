<?php

namespace App\Console\Commands;

use App\Services\GeographicalDataService;
use Illuminate\Console\Command;

class GeographicalDataStatus extends Command
{
    protected $signature = 'geo:status';
    protected $description = 'Check the status of geographical data';

    public function handle(GeographicalDataService $geoService)
    {
        $this->info('🌍 Geographical Data Status');
        $this->newLine();

        // Get current data counts
        $counts = $geoService->getDataCounts();

        $this->info('📊 Current Data Counts:');
        foreach ($counts as $table => $count) {
            $status = $count > 0 ? '✅' : '❌';
            $this->line("   {$status} {$table}: " . number_format($count) . ' records');
        }

        $this->newLine();

        // Check if data exists
        $hasData = $geoService->hasExistingData();

        if ($hasData) {
            $this->info('✅ Geographical data is available');
        } else {
            $this->warn('⚠️  No geographical data found');
            $this->line('   Run: php artisan geo:sync');
        }

        $this->newLine();

        // Show configuration status
        $config = config('geographical');

        $this->info('⚙️  Configuration:');
        $this->line('   Sync enabled: ' . ($config['sync']['enabled'] ? '✅ Yes' : '❌ No'));
        $this->line('   Backup enabled: ' . ($config['backup']['enabled'] ? '✅ Yes' : '❌ No'));
        $this->line('   Notifications enabled: ' . ($config['notifications']['enabled'] ? '✅ Yes' : '❌ No'));

        return 0;
    }
}
