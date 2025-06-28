<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Geographical Data Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for geographical data synchronization from external sources
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Data Source
    |--------------------------------------------------------------------------
    |
    | URL for downloading geographical data
    |
    */
    'source_url' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/world.sql',

    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatic synchronization
    |
    */
    'sync' => [
        'enabled' => env('GEO_SYNC_ENABLED', true),
        'timeout' => env('GEO_SYNC_TIMEOUT', 120), // seconds
        'retry_attempts' => env('GEO_SYNC_RETRY_ATTEMPTS', 3),
        'chunk_size' => env('GEO_SYNC_CHUNK_SIZE', 1000), // for batch processing
    ],

    /*
    |--------------------------------------------------------------------------
    | Tables Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for geographical tables
    |
    */
    'tables' => [
        'regions' => [
            'enabled' => true,
            'preserve_custom' => false, // Whether to preserve custom entries
        ],
        'subregions' => [
            'enabled' => true,
            'preserve_custom' => false,
        ],
        'countries' => [
            'enabled' => true,
            'preserve_custom' => false,
        ],
        'states' => [
            'enabled' => true,
            'preserve_custom' => false,
        ],
        'cities' => [
            'enabled' => true,
            'preserve_custom' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Settings
    |--------------------------------------------------------------------------
    |
    | Settings for backing up data before sync
    |
    */
    'backup' => [
        'enabled' => env('GEO_BACKUP_ENABLED', true),
        'path' => storage_path('app/backups/geographical'),
        'keep_days' => env('GEO_BACKUP_KEEP_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Email notifications for sync operations
    |
    */
    'notifications' => [
        'enabled' => env('GEO_NOTIFICATIONS_ENABLED', true),
        'admin_email' => env('GEO_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS', 'admin@example.com')),
        'notify_on' => [
            'success' => env('GEO_NOTIFY_SUCCESS', false),
            'failure' => env('GEO_NOTIFY_FAILURE', true),
            'large_changes' => env('GEO_NOTIFY_LARGE_CHANGES', true), // If more than X% of data changes
        ],
        'large_change_threshold' => 10, // percentage
    ],
];
