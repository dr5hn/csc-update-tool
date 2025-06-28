<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class GeographicalDataService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('geographical');
    }

    /**
     * Sync geographical data from external source
     */
    public function sync(bool $force = false): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'data' => [],
            'errors' => []
        ];

        try {
            // Check if sync is enabled
            if (!$this->config['sync']['enabled']) {
                $result['message'] = 'Geographical data sync is disabled';
                return $result;
            }

            // Check if we should skip if data exists
            if (!$force && $this->hasExistingData()) {
                $result['success'] = true;
                $result['message'] = 'Geographical data already exists';
                return $result;
            }

            // Create backup if enabled
            if ($this->config['backup']['enabled']) {
                $this->createBackup();
            }

            // Download and process data
            $sqlContent = $this->downloadSqlFile();
            if (!$sqlContent) {
                throw new Exception('Failed to download SQL file from external source');
            }

            $counts = $this->processSqlContent($sqlContent);

            $result['success'] = true;
            $result['message'] = 'Geographical data synced successfully';
            $result['data'] = $counts;

            // Auto-verify incorporated changes after sync
            $this->verifyIncorporatedChanges();

            Log::info('Geographical data sync completed', $counts);

        } catch (Exception $e) {
            $result['errors'][] = $e->getMessage();
            $result['message'] = 'Sync failed: ' . $e->getMessage();

            Log::error('Geographical data sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $result;
    }

    /**
     * Check if geographical data exists
     */
    public function hasExistingData(): bool
    {
        try {
            $counts = $this->getDataCounts();
            return $counts['countries'] > 0 || $counts['states'] > 0 || $counts['cities'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get current data counts
     */
    public function getDataCounts(): array
    {
        $counts = [];

        foreach (array_keys($this->config['tables']) as $table) {
            try {
                $counts[$table] = DB::table($table)->count();
            } catch (Exception $e) {
                $counts[$table] = 0;
            }
        }

        return $counts;
    }

    /**
     * Download SQL file from source
     */
    private function downloadSqlFile(): ?string
    {
        $timeout = $this->config['sync']['timeout'];
        $retryAttempts = $this->config['sync']['retry_attempts'];
        $sourceUrl = $this->config['source_url'];

        for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
            try {
                $response = Http::timeout($timeout)->get($sourceUrl);

                if ($response->successful()) {
                    return $response->body();
                }

                Log::warning("Download attempt {$attempt} failed", [
                    'url' => $sourceUrl,
                    'status' => $response->status()
                ]);

            } catch (Exception $e) {
                Log::warning("Download attempt {$attempt} failed", [
                    'url' => $sourceUrl,
                    'error' => $e->getMessage()
                ]);
            }

            // Wait before retry (except on last attempt)
            if ($attempt < $retryAttempts) {
                sleep(2);
            }
        }

        return null;
    }

    /**
     * Process SQL content and update database
     */
    private function processSqlContent(string $sqlContent): array
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Get counts before clearing
            $beforeCounts = $this->getDataCounts();

            // Clear existing data
            $this->clearExistingData();

            // Parse and execute SQL statements
            $statements = $this->parseSqlStatements($sqlContent);

            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    try {
                        DB::unprepared($statement);
                    } catch (Exception $e) {
                        // Log but continue - some statements might fail due to schema differences
                        Log::warning('SQL statement failed during sync', [
                            'statement' => substr($statement, 0, 200),
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Get counts after sync
            $afterCounts = $this->getDataCounts();

            return [
                'before' => $beforeCounts,
                'after' => $afterCounts,
                'statements_processed' => count($statements)
            ];

        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Clear existing geographical data
     */
    private function clearExistingData(): void
    {
        // Clear in correct order to respect foreign keys
        $tables = ['cities', 'states', 'countries', 'subregions', 'regions'];

        foreach ($tables as $table) {
            if ($this->config['tables'][$table]['enabled']) {
                DB::table($table)->delete();
            }
        }
    }

    /**
     * Parse SQL statements from content
     */
    private function parseSqlStatements(string $sqlContent): array
    {
        // Remove comments
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

        // Split by semicolon but be careful with semicolons inside strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = null;

        for ($i = 0; $i < strlen($sqlContent); $i++) {
            $char = $sqlContent[$i];

            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar) {
                // Check if it's escaped
                if ($i > 0 && $sqlContent[$i - 1] !== '\\') {
                    $inString = false;
                    $stringChar = null;
                }
            } elseif (!$inString && $char === ';') {
                if (!empty(trim($current))) {
                    $statements[] = trim($current);
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        // Add the last statement if it exists
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }

        return $statements;
    }

    /**
     * Create backup of current data
     */
    private function createBackup(): void
    {
        try {
            $backupPath = $this->config['backup']['path'];
            $timestamp = now()->format('Y-m-d_H-i-s');

            // Ensure backup directory exists
            if (!Storage::exists($backupPath)) {
                Storage::makeDirectory($backupPath);
            }

            // Create backup for each table
            foreach (array_keys($this->config['tables']) as $table) {
                if ($this->config['tables'][$table]['enabled']) {
                    $data = DB::table($table)->get();
                    $filename = "{$backupPath}/{$table}_{$timestamp}.json";
                    Storage::put($filename, $data->toJson());
                }
            }

            // Clean old backups
            $this->cleanOldBackups();

            Log::info('Geographical data backup created', ['timestamp' => $timestamp]);

        } catch (Exception $e) {
            Log::warning('Failed to create backup', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Clean old backup files
     */
    private function cleanOldBackups(): void
    {
        try {
            $keepDays = $this->config['backup']['keep_days'];
            $cutoffDate = now()->subDays($keepDays);

            $backupPath = $this->config['backup']['path'];
            $files = Storage::files($backupPath);

            foreach ($files as $file) {
                $fileTime = Storage::lastModified($file);
                if ($fileTime < $cutoffDate->timestamp) {
                    Storage::delete($file);
                }
            }

        } catch (Exception $e) {
            Log::warning('Failed to clean old backups', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Verify incorporated changes after sync
     */
    private function verifyIncorporatedChanges(): void
    {
        try {
            $incorporationService = app(ChangeRequestIncorporationService::class);
            $results = $incorporationService->verifyAgainstSync();

            Log::info('Post-sync verification completed', [
                'total_checked' => $results['total_checked'],
                'verified' => count($results['verified']),
                'missing' => count($results['missing']),
                'conflicted' => count($results['conflicted'])
            ]);

            // Log missing changes for attention
            if (!empty($results['missing'])) {
                Log::warning('Some incorporated changes are missing from sync', [
                    'missing_change_request_ids' => $results['missing']
                ]);
            }

            // Log conflicted changes for urgent attention
            if (!empty($results['conflicted'])) {
                Log::error('Some incorporated changes have conflicts with sync', [
                    'conflicted_change_request_ids' => $results['conflicted']
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to verify incorporated changes after sync', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
