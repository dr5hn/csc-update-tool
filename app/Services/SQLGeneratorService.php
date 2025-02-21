<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class SQLGeneratorService 
{
    /**
     * Process the change request and generate SQL statements
     */
    public function generate(array $data): array 
    {
        try {
            $sql = [
                'up' => $this->generateUpSQL($data),
                'down' => $this->generateDownSQL($data),
            ];

            return [
                'success' => true,
                'data' => $sql
            ];
        } catch (Exception $e) {
            Log::error('SQL Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate the forward SQL statements (migrations up)
     */
    private function generateUpSQL(array $data): string 
    {
        $sqlParts = [];
        $sqlParts[] = "-- Generated SQL for change request\n";
        $sqlParts[] = "START TRANSACTION;\n";

        // Process Modifications
        if (!empty($data['modifications'])) {
            $sqlParts[] = "\n-- Modifications";
            foreach ($data['modifications'] as $table => $records) {
                foreach ($records as $id => $record) {
                    if (!is_array($record)) {
                        throw new Exception("Invalid record format for modification in table {$table}");
                    }
                    $sqlParts[] = $this->generateModificationSQL($table, $record);
                }
            }
        }

        // Process Additions
        if (!empty($data['additions'])) {
            $sqlParts[] = "\n-- Additions";
            foreach ($data['additions'] as $key => $record) {
                if (!is_array($record)) {
                    throw new Exception("Invalid record format for addition");
                }
                
                // Extract table name from the key (format: "added-table_id")
                $parts = explode('-', $key);
                if (count($parts) < 2) {
                    throw new Exception("Invalid addition key format: {$key}");
                }
                
                $tableParts = explode('_', $parts[1]);
                $table = $tableParts[0];
                
                $sqlParts[] = $this->generateAdditionSQL($table, $record);
            }
        }

        // Process Deletions
        if (!empty($data['deletions'])) {
            $sqlParts[] = "\n-- Deletions";
            foreach ($data['deletions'] as $deletion) {
                if (!is_string($deletion)) {
                    throw new Exception("Invalid deletion format");
                }
                $sqlParts[] = $this->generateDeletionSQL($deletion);
            }
        }

        $sqlParts[] = "\nCOMMIT;";
        
        return implode("\n", $sqlParts);
    }

    /**
     * Generate the rollback SQL statements (migrations down)
     */
    private function generateDownSQL(array $data): string 
    {
        $sqlParts = [];
        $sqlParts[] = "-- Rollback SQL for change request\n";
        $sqlParts[] = "START TRANSACTION;\n";

        // Rollback Deletions (restore deleted records)
        if (!empty($data['deletions'])) {
            $sqlParts[] = "\n-- Restore Deleted Records";
            foreach ($data['deletions'] as $deletion) {
                $sqlParts[] = $this->generateRestoreSQL($deletion);
            }
        }

        // Rollback Modifications (revert to original values)
        if (!empty($data['modifications'])) {
            $sqlParts[] = "\n-- Revert Modified Records";
            foreach ($data['modifications'] as $table => $records) {
                foreach ($records as $id => $record) {
                    if (!is_array($record)) {
                        throw new Exception("Invalid record format for modification in table {$table}");
                    }
                    $sqlParts[] = $this->generateRevertSQL($table, $record);
                }
            }
        }

        // Rollback Additions (remove added records)
        if (!empty($data['additions'])) {
            $sqlParts[] = "\n-- Remove Added Records";
            foreach ($data['additions'] as $key => $record) {
                if (!is_array($record)) {
                    throw new Exception("Invalid record format for addition");
                }
                
                $parts = explode('-', $key);
                if (count($parts) < 2) {
                    throw new Exception("Invalid addition key format: {$key}");
                }
                
                $tableParts = explode('_', $parts[1]);
                $table = $tableParts[0];
                
                $sqlParts[] = $this->generateRemoveSQL($table, $record);
            }
        }

        $sqlParts[] = "\nCOMMIT;";
        
        return implode("\n", $sqlParts);
    }

    /**
     * Generate SQL for modifications
     */
    private function generateModificationSQL(string $table, array $record): string 
    {
        if (empty($record['id'])) {
            throw new Exception("Missing ID for modification in table {$table}");
        }

        $sets = [];
        foreach ($record as $column => $value) {
            if ($column === 'id') continue;
            $sets[] = sprintf("%s = %s", $column, $this->formatValue($value));
        }

        return sprintf(
            "UPDATE %s SET %s WHERE id = %d;",
            $table,
            implode(', ', $sets),
            $record['id']
        );
    }

    /**
     * Generate SQL for additions
     */
    private function generateAdditionSQL(string $table, array $record): string 
    {
        $columns = array_keys($record);
        $values = array_map([$this, 'formatValue'], array_values($record));

        return sprintf(
            "INSERT INTO %s (%s) VALUES (%s);",
            $table,
            implode(', ', $columns),
            implode(', ', $values)
        );
    }

    /**
     * Generate SQL for deletions
     */
    private function generateDeletionSQL(string $deletion): string 
    {
        list($table, $id) = explode('_', $deletion);
        return sprintf("DELETE FROM %s WHERE id = %d;", $table, $id);
    }

    /**
     * Generate SQL to restore a deleted record
     */
    private function generateRestoreSQL(string $deletion): string 
    {
        list($table, $id) = explode('_', $deletion);
        // Note: This would need the original record data to properly restore
        return "-- Restore data for {$table} id {$id} (needs original data)";
    }

    /**
     * Generate SQL to revert modifications
     */
    private function generateRevertSQL(string $table, array $record): string 
    {
        if (empty($record['id'])) {
            throw new Exception("Missing ID for revert in table {$table}");
        }

        // Note: This would need the original record data to properly revert
        return sprintf(
            "-- Revert changes for %s id %d (needs original data)",
            $table,
            $record['id']
        );
    }

    /**
     * Generate SQL to remove added records
     */
    private function generateRemoveSQL(string $table, array $record): string 
    {
        if (empty($record['id'])) {
            throw new Exception("Missing ID for removal in table {$table}");
        }

        return sprintf(
            "DELETE FROM %s WHERE id = %d;",
            $table,
            $record['id']
        );
    }

    /**
     * Format a value for SQL
     */
    private function formatValue($value): string 
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            return sprintf("'%s'", addslashes(json_encode($value)));
        }

        return sprintf("'%s'", addslashes($value));
    }
}