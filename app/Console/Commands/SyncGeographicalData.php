<?php

namespace App\Console\Commands;

use App\Services\GeographicalDataService;
use Illuminate\Console\Command;

class SyncGeographicalData extends Command
{
    protected $signature = 'geo:sync {--force : Force sync even if data exists}';
    protected $description = 'Sync geographical data from countries-states-cities-database';

    public function handle(GeographicalDataService $geoService)
    {
        $this->info('🌍 Starting geographical data sync...');

        $result = $geoService->sync($this->option('force'));

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);

            if (!empty($result['data'])) {
                $this->displayDataCounts($result['data']);
            }

            return 0;
        } else {
            $this->error('❌ ' . $result['message']);

            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->error('   - ' . $error);
                }
            }

            return 1;
        }
    }

        private function displayDataCounts(array $data): void
    {
        $this->newLine();
        $this->info('📈 Data Summary:');

        if (isset($data['after'])) {
            foreach ($data['after'] as $table => $count) {
                $this->line("   {$table}: " . number_format($count) . ' records');
            }
        }

        if (isset($data['statements_processed'])) {
            $this->line("   SQL statements processed: " . number_format($data['statements_processed']));
        }
    }
}
