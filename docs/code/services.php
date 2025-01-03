<?php

// app/Services/ChangeRequestService.php
namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChangeRequestService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $changeRequest = ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'table_name' => $data['table_name'],
                'change_type' => $data['change_type'],
                'new_data' => $data['new_data'],
                'original_data' => $data['original_data'] ?? null,
                'status' => 'pending'
            ]);

            $this->logAudit(
                'create',
                'change_requests',
                $changeRequest->id,
                null,
                $changeRequest->toArray()
            );

            return $changeRequest;
        });
    }

    public function approve(ChangeRequest $changeRequest)
    {
        return DB::transaction(function () use ($changeRequest) {
            $oldStatus = $changeRequest->status;

            $changeRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            $this->logAudit(
                'approve',
                'change_requests',
                $changeRequest->id,
                ['status' => $oldStatus],
                ['status' => 'approved']
            );

            // Apply the approved changes to the actual table
            $this->applyChanges($changeRequest);

            return $changeRequest;
        });
    }

    public function reject(ChangeRequest $changeRequest, string $reason)
    {
        return DB::transaction(function () use ($changeRequest, $reason) {
            $oldStatus = $changeRequest->status;

            $changeRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Add rejection comment
            $changeRequest->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $reason
            ]);

            $this->logAudit(
                'reject',
                'change_requests',
                $changeRequest->id,
                ['status' => $oldStatus],
                ['status' => 'rejected']
            );

            return $changeRequest;
        });
    }

    protected function applyChanges(ChangeRequest $changeRequest)
    {
        $table = $changeRequest->table_name;
        $data = $changeRequest->new_data;

        switch ($changeRequest->change_type) {
            case 'add':
                DB::table($table)->insert($data);
                break;

            case 'update':
                $id = $data['id'] ?? null;
                if ($id) {
                    unset($data['id']);
                    DB::table($table)->where('id', $id)->update($data);
                }
                break;

            case 'delete':
                $id = $data['id'] ?? null;
                if ($id) {
                    DB::table($table)->where('id', $id)->delete();
                }
                break;
        }
    }

    protected function logAudit(
        string $action,
        string $tableName,
        int $recordId,
        ?array $oldValues,
        ?array $newValues
    ) {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}

// app/Services/ValidationService.php
namespace App\Services;

class ValidationService
{
    public function validateChangeRequest(array $data, string $tableName)
    {
        // Get table structure
        $columns = $this->getTableColumns($tableName);
        
        // Validate data against table structure
        foreach ($data as $column => $value) {
            if (!isset($columns[$column])) {
                throw new \Exception("Invalid column: {$column}");
            }

            $this->validateDataType($value, $columns[$column]);
        }

        return true;
    }

    protected function getTableColumns(string $tableName)
    {
        return DB::getSchemaBuilder()->getColumnListing($tableName);
    }

    protected function validateDataType($value, $columnType)
    {
        // Add specific data type validation logic here
        // This is a placeholder for actual validation logic
        return true;
    }
}
