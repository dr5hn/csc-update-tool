<?php

namespace App\Console\Commands;

use App\Services\ChangeRequestIncorporationService;
use App\Models\ChangeRequest;
use Illuminate\Console\Command;

class MarkChangesIncorporated extends Command
{
    protected $signature = 'changes:mark-incorporated
                            {ids? : Comma-separated change request IDs}
                            {--all-approved : Mark all approved requests as incorporated}
                            {--by= : Who incorporated the changes}';

    protected $description = 'Mark change requests as incorporated into the main database';

    public function handle(ChangeRequestIncorporationService $incorporationService)
    {
        $this->info('🔄 Marking change requests as incorporated...');

        // Determine which change requests to mark
        $changeRequestIds = $this->getChangeRequestIds();

        if (empty($changeRequestIds)) {
            $this->error('❌ No change requests specified');
            return 1;
        }

        // Get who incorporated the changes
        $incorporatedBy = $this->option('by') ?: $this->ask('Who incorporated these changes?');

        if (!$incorporatedBy) {
            $this->error('❌ Incorporator name is required');
            return 1;
        }

        // Show what will be marked
        $this->info("📝 Change requests to mark as incorporated: " . implode(', ', $changeRequestIds));
        $this->info("👤 Incorporated by: {$incorporatedBy}");

        if (!$this->confirm('Continue?')) {
            $this->info('❌ Operation cancelled');
            return 0;
        }

        // Mark as incorporated
        $results = $incorporationService->markAsIncorporated(
            $changeRequestIds,
            $incorporatedBy,
            ['marked_via_command' => true, 'timestamp' => now()->toISOString()]
        );

        // Display results
        $this->newLine();
        $this->info("✅ Successfully marked {$results['total']} change requests:");

        if (!empty($results['success'])) {
            $this->info("   ✓ Success: " . implode(', ', $results['success']));
        }

        if (!empty($results['failed'])) {
            $this->error("   ✗ Failed:");
            foreach ($results['failed'] as $failure) {
                $this->error("     - ID {$failure['id']}: {$failure['reason']}");
            }
        }

        return 0;
    }

    private function getChangeRequestIds(): array
    {
        if ($this->option('all-approved')) {
            return ChangeRequest::where('status', 'approved')
                ->where('incorporation_status', 'pending')
                ->pluck('id')
                ->toArray();
        }

        $idsInput = $this->argument('ids');
        if ($idsInput) {
            return array_map('trim', explode(',', $idsInput));
        }

        // Interactive selection
        $pendingRequests = ChangeRequest::where('status', 'approved')
            ->where('incorporation_status', 'pending')
            ->get(['id', 'title']);

        if ($pendingRequests->isEmpty()) {
            $this->warn('⚠️  No pending approved change requests found');
            return [];
        }

        $this->info('📋 Pending approved change requests:');
        foreach ($pendingRequests as $request) {
            $this->line("   {$request->id}: {$request->title}");
        }

        $selected = $this->ask('Enter change request IDs (comma-separated) or "all" for all pending');

        if ($selected === 'all') {
            return $pendingRequests->pluck('id')->toArray();
        }

        return array_map('trim', explode(',', $selected));
    }
}
