<?php

namespace App\Console\Commands;

use App\Services\ChangeRequestIncorporationService;
use Illuminate\Console\Command;

class VerifyIncorporatedChanges extends Command
{
    protected $signature = 'changes:verify-incorporated
                            {--auto : Run automatically without prompts}';

    protected $description = 'Verify incorporated changes against current database after sync';

    public function handle(ChangeRequestIncorporationService $incorporationService)
    {
        $this->info('🔍 Verifying incorporated changes against current database...');

        if (!$this->option('auto')) {
            $this->warn('⚠️  This will check all incorporated changes against the current database.');
            $this->warn('   Run this after syncing with external data to see what changes are missing.');

            if (!$this->confirm('Continue?')) {
                $this->info('❌ Operation cancelled');
                return 0;
            }
        }

        // Run verification
        $results = $incorporationService->verifyAgainstSync();

        // Display results
        $this->newLine();
        $this->info("📊 Verification Results:");
        $this->info("   Total checked: {$results['total_checked']}");

        if (!empty($results['verified'])) {
            $this->info("   ✅ Verified (present in sync): " . count($results['verified']));
            if (!$this->option('auto')) {
                $this->line("      IDs: " . implode(', ', $results['verified']));
            }
        }

        if (!empty($results['missing'])) {
            $this->warn("   ⚠️  Missing from sync: " . count($results['missing']));
            $this->warn("      IDs: " . implode(', ', $results['missing']));
            $this->newLine();
            $this->warn("   📝 These changes need to be re-incorporated in the next release:");
            foreach ($results['missing'] as $id) {
                $changeRequest = \App\Models\ChangeRequest::find($id);
                if ($changeRequest) {
                    $this->warn("      - #{$id}: {$changeRequest->title}");
                }
            }
        }

        if (!empty($results['conflicted'])) {
            $this->error("   ❌ Conflicted (data mismatch): " . count($results['conflicted']));
            $this->error("      IDs: " . implode(', ', $results['conflicted']));
            $this->newLine();
            $this->error("   🔧 These changes have conflicts and need manual review:");
            foreach ($results['conflicted'] as $id) {
                $changeRequest = \App\Models\ChangeRequest::find($id);
                if ($changeRequest) {
                    $this->error("      - #{$id}: {$changeRequest->title}");
                }
            }
        }

        // Generate summary report
        if (!$this->option('auto')) {
            $this->newLine();
            $this->info('📋 Generating detailed summary...');
            $summary = $incorporationService->getIncorporationSummary();
            $this->displaySummary($summary);
        }

        return 0;
    }

    private function displaySummary(array $summary): void
    {
        $this->newLine();
        $this->info('📈 Incorporation Status Summary:');

        $this->info("   Total approved requests: {$summary['total_approved']}");

        if (!empty($summary['by_incorporation_status'])) {
            $this->info('   By status:');
            foreach ($summary['by_incorporation_status'] as $status => $count) {
                $icon = $this->getStatusIcon($status);
                $this->line("      {$icon} {$status}: {$count}");
            }
        }

        if (!empty($summary['pending_incorporation'])) {
            $this->newLine();
            $this->warn('⏳ Pending incorporation (' . count($summary['pending_incorporation']) . '):');
            foreach (array_slice($summary['pending_incorporation'], 0, 5) as $request) {
                $this->warn("   - #{$request['id']}: {$request['title']}");
            }
            if (count($summary['pending_incorporation']) > 5) {
                $remaining = count($summary['pending_incorporation']) - 5;
                $this->warn("   ... and {$remaining} more");
            }
        }

        if (!empty($summary['verification_needed'])) {
            $this->newLine();
            $this->info('🔍 Need verification (' . count($summary['verification_needed']) . '):');
            foreach (array_slice($summary['verification_needed'], 0, 5) as $request) {
                $this->info("   - #{$request['id']}: {$request['title']}");
            }
            if (count($summary['verification_needed']) > 5) {
                $remaining = count($summary['verification_needed']) - 5;
                $this->info("   ... and {$remaining} more");
            }
        }

        if (!empty($summary['recent_incorporations'])) {
            $this->newLine();
            $this->info('📅 Recent incorporations (last 7 days):');
            foreach (array_slice($summary['recent_incorporations'], 0, 5) as $request) {
                $date = \Carbon\Carbon::parse($request['incorporated_at'])->format('M j, Y');
                $this->info("   - #{$request['id']}: {$request['title']} ({$date} by {$request['incorporated_by']})");
            }
        }
    }

    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => '⏳',
            'incorporated' => '🔄',
            'verified' => '✅',
            'missing' => '⚠️',
            'conflicted' => '❌',
            default => '❓'
        };
    }
}
