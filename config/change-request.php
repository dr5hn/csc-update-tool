<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Change Request Configuration
    |--------------------------------------------------------------------------
    */

    'statuses' => [
        'draft' => 'Draft',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ],

    'change_types' => [
        'add' => 'Add',
        'update' => 'Update',
        'delete' => 'Delete'
    ],

    'supported_tables' => [
        'countries' => 'Countries',
        'states' => 'States',
        'cities' => 'Cities',
        'regions' => 'Regions',
        'subregions' => 'Subregions'
    ],

    'roles' => [
        'user' => 'User',
        'admin' => 'Admin'
    ],

    // Pagination settings
    'pagination' => [
        'per_page' => 25,
        'max_per_page' => 100,
        'options' => [10, 25, 50, 100]
    ],

    // File upload settings for supporting documents
    'attachments' => [
        'enabled' => false, // Future feature
        'max_size' => 2048, // 2MB in kilobytes
        'allowed_types' => [
            'application/pdf',
            'text/plain',
            'text/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        'max_files' => 3,
        'storage_disk' => 'local',
        'storage_path' => 'change-requests/attachments'
    ],

    // Cache settings
    'cache' => [
        'enabled' => env('CHANGE_REQUEST_CACHE_ENABLED', true),
        'ttl' => env('CHANGE_REQUEST_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'change_request_',
        'geographical_data_ttl' => 7200, // 2 hours for geographical data
        'stats_ttl' => 1800, // 30 minutes for statistics
    ],

    // Rate limiting
    'rate_limits' => [
        'create_request' => '5,1', // 5 requests per minute
        'submit_request' => '3,1', // 3 submissions per minute
        'comment' => '10,1', // 10 comments per minute
        'api_general' => '60,1', // 60 API calls per minute
    ],

    // Notification settings
    'notifications' => [
        'mail' => [
            'enabled' => env('CHANGE_REQUEST_NOTIFICATIONS_ENABLED', true),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'name' => env('MAIL_FROM_NAME', 'CSC Update Tool'),
            ],
            'admin_emails' => env('ADMIN_NOTIFICATION_EMAILS', ''),
        ],
        'channels' => ['mail'], // Future: could add 'slack', 'teams', etc.
        'events' => [
            'request_submitted' => true,
            'request_approved' => true,
            'request_rejected' => true,
            'comment_added' => true,
        ]
    ],

    // SQL Generation settings
    'sql_generation' => [
        'include_transactions' => true,
        'include_rollback' => true,
        'add_timestamps' => true,
        'add_comments' => true,
        'validate_syntax' => false, // Future feature
    ],

    // Security settings
    'security' => [
        'max_data_size' => 1048576, // 1MB max JSON data size
        'allowed_operations_per_request' => 100,
        'require_admin_approval' => true,
        'auto_approve_minor_changes' => false, // Future feature
    ],

    // Performance settings
    'performance' => [
        'lazy_load_cities' => true,
        'chunk_size' => 1000,
        'max_search_results' => 500,
        'enable_query_optimization' => true,
    ],

    // Audit settings
    'audit' => [
        'enabled' => env('CHANGE_REQUEST_AUDIT_ENABLED', true),
        'log_level' => env('CHANGE_REQUEST_AUDIT_LOG_LEVEL', 'info'),
        'include_user_agent' => true,
        'include_ip_address' => true,
        'retention_days' => env('CHANGE_REQUEST_AUDIT_RETENTION_DAYS', 365),
    ],
];
