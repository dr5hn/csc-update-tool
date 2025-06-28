<?php

namespace App\Services;

use App\Models\ChangeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChangeRequestIncorporationService
{
    /**
     * Mark change requests as incorporated
     */
    public function markAsIncorporated(array $changeRequestIds, string $incorporatedBy, array $details = []): array
    {
        $results = [
            'success' => [],
            'failed' => [],
            'total' => count($changeRequestIds)
        ];

        foreach ($changeRequestIds as $id) {
            try {
                $changeRequest = ChangeRequest::find($id);

                if (!$changeRequest) {
                    $results['failed'][] = ['id' => $id, 'reason' => 'Change request not found'];
                    continue;
                }

                if ($changeRequest->status !== 'approved') {
                    $results['failed'][] = ['id' => $id, 'reason' => 'Not approved'];
                    continue;
                }

                $changeRequest->update([
                    'incorporation_status' => 'incorporated',
                    'incorporated_at' => now(),
                    'incorporated_by' => $incorporatedBy,
                    'incorporation_details' => $details,
                ]);

                $results['success'][] = $id;

                Log::info('Change request marked as incorporated', [
                    'change_request_id' => $id,
                    'incorporated_by' => $incorporatedBy
                ]);

            } catch (\Exception $e) {
                $results['failed'][] = ['id' => $id, 'reason' => $e->getMessage()];
                Log::error('Failed to mark change request as incorporated', [
                    'change_request_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Verify incorporated changes against synced data
     */
    public function verifyAgainstSync(): array
    {
        $incorporatedRequests = ChangeRequest::where('status', 'approved')
            ->whereIn('incorporation_status', ['incorporated', 'verified', 'missing'])
            ->get();

        $results = [
            'verified' => [],
            'missing' => [],
            'conflicted' => [],
            'total_checked' => $incorporatedRequests->count()
        ];

        foreach ($incorporatedRequests as $request) {
            try {
                $verificationResult = $this->verifyChangeRequest($request);

                $request->update([
                    'incorporation_status' => $verificationResult['status'],
                    'last_sync_verified_at' => now(),
                    'sync_verification_details' => $verificationResult['details']
                ]);

                $results[$verificationResult['status']][] = $request->id;

            } catch (\Exception $e) {
                Log::error('Failed to verify change request', [
                    'change_request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Verify a single change request against current database
     */
    private function verifyChangeRequest(ChangeRequest $request): array
    {
        $changes = json_decode($request->new_data, true);
        $details = [
            'verification_date' => now()->toISOString(),
            'changes_verified' => [],
            'issues' => []
        ];

        $allVerified = true;
        $hasConflicts = false;

        // Verify modifications
        if (!empty($changes['modifications'])) {
            foreach ($changes['modifications'] as $table => $modifications) {
                foreach ($modifications as $recordKey => $newData) {
                    $verification = $this->verifyModification($table, $recordKey, $newData);
                    $details['changes_verified'][] = $verification;

                    if (!$verification['verified']) {
                        $allVerified = false;
                        if ($verification['type'] === 'conflict') {
                            $hasConflicts = true;
                        }
                    }
                }
            }
        }

        // Verify additions
        if (!empty($changes['additions'])) {
            foreach ($changes['additions'] as $recordKey => $newData) {
                $verification = $this->verifyAddition($recordKey, $newData);
                $details['changes_verified'][] = $verification;

                if (!$verification['verified']) {
                    $allVerified = false;
                }
            }
        }

        // Verify deletions
        if (!empty($changes['deletions'])) {
            foreach ($changes['deletions'] as $recordKey) {
                $verification = $this->verifyDeletion($recordKey);
                $details['changes_verified'][] = $verification;

                if (!$verification['verified']) {
                    $allVerified = false;
                }
            }
        }

        // Determine status
        $status = 'verified';
        if ($hasConflicts) {
            $status = 'conflicted';
        } elseif (!$allVerified) {
            $status = 'missing';
        }

        return [
            'status' => $status,
            'details' => $details
        ];
    }

    /**
     * Verify a modification exists in current database
     */
    private function verifyModification(string $table, string $recordKey, array $expectedData): array
    {
        $parts = explode('_', $recordKey);
        $id = $parts[1] ?? null;

        if (!$id) {
            return [
                'type' => 'modification',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        try {
            $currentRecord = DB::table($table)->where('id', $id)->first();

            if (!$currentRecord) {
                return [
                    'type' => 'modification',
                    'table' => $table,
                    'record_key' => $recordKey,
                    'verified' => false,
                    'reason' => 'Record not found'
                ];
            }

            $currentRecord = (array) $currentRecord;
            $mismatches = [];

            foreach ($expectedData as $field => $expectedValue) {
                if ($field === 'id') continue;

                $currentValue = $currentRecord[$field] ?? null;
                if ((string)$currentValue !== (string)$expectedValue) {
                    $mismatches[] = [
                        'field' => $field,
                        'expected' => $expectedValue,
                        'current' => $currentValue
                    ];
                }
            }

            if (!empty($mismatches)) {
                return [
                    'type' => 'conflict',
                    'table' => $table,
                    'record_key' => $recordKey,
                    'verified' => false,
                    'reason' => 'Data conflicts found',
                    'mismatches' => $mismatches
                ];
            }

            return [
                'type' => 'modification',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => true,
                'reason' => 'Data matches expected values'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'modification',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify an addition exists in current database
     */
    private function verifyAddition(string $recordKey, array $expectedData): array
    {
        // Extract table and ID from record key (format: added-table_id)
        $parts = explode('-', $recordKey);
        if (count($parts) < 2) {
            return [
                'type' => 'addition',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        $tableParts = explode('_', $parts[1]);
        $table = $tableParts[0];
        $id = $tableParts[1] ?? null;

        if (!$id) {
            return [
                'type' => 'addition',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'No ID found in record key'
            ];
        }

        try {
            // Check if record exists with expected data
            $query = DB::table($table)->where('id', $id);

            // Add other fields to query for exact match
            foreach ($expectedData as $field => $value) {
                if ($field !== 'id') {
                    $query->where($field, $value);
                }
            }

            $exists = $query->exists();

            return [
                'type' => 'addition',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => $exists,
                'reason' => $exists ? 'Record found with expected data' : 'Record not found or data mismatch'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'addition',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify a deletion was applied in current database
     */
    private function verifyDeletion(string $recordKey): array
    {
        $parts = explode('_', $recordKey);
        $table = $parts[0];
        $id = $parts[1] ?? null;

        if (!$id) {
            return [
                'type' => 'deletion',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        try {
            $exists = DB::table($table)->where('id', $id)->exists();

            return [
                'type' => 'deletion',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => !$exists, // Verified if record does NOT exist
                'reason' => $exists ? 'Record still exists (deletion not applied)' : 'Record successfully deleted'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'deletion',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get incorporation status summary
     */
    public function getIncorporationSummary(): array
    {
        $approvedRequests = ChangeRequest::where('status', 'approved')->get();

        $summary = [
            'total_approved' => $approvedRequests->count(),
            'by_incorporation_status' => [],
            'recent_incorporations' => [],
            'pending_incorporation' => [],
            'verification_needed' => []
        ];

        // Group by incorporation status
        $groupedByStatus = $approvedRequests->groupBy('incorporation_status');
        foreach ($groupedByStatus as $status => $requests) {
            $summary['by_incorporation_status'][$status] = $requests->count();
        }

        // Recent incorporations (last 7 days)
        $summary['recent_incorporations'] = ChangeRequest::where('status', 'approved')
            ->where('incorporated_at', '>=', now()->subDays(7))
            ->orderBy('incorporated_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'incorporated_at', 'incorporated_by'])
            ->toArray();

        // Pending incorporation
        $summary['pending_incorporation'] = ChangeRequest::where('status', 'approved')
            ->where('incorporation_status', 'pending')
            ->get(['id', 'title', 'created_at'])
            ->toArray();

        // Need verification (incorporated but not verified recently)
        $summary['verification_needed'] = ChangeRequest::where('status', 'approved')
            ->where('incorporation_status', 'incorporated')
            ->where(function($query) {
                $query->whereNull('last_sync_verified_at')
                      ->orWhere('last_sync_verified_at', '<', now()->subDays(7));
            })
            ->get(['id', 'title', 'incorporated_at', 'last_sync_verified_at'])
            ->toArray();

        return $summary;
    }
}
