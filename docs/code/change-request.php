// config/change-request.php
return [
    /*
    |--------------------------------------------------------------------------
    | Change Request Configuration
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ],

    'change_types' => [
        'add' => 'Add',
        'update' => 'Update',
        'delete' => 'Delete'
    ],

    'tables' => [
        'countries' => 'Countries',
        'states' => 'States',
        'cities' => 'Cities'
    ],

    'roles' => [
        'user' => 'User',
        'moderator' => 'Moderator',
        'admin' => 'Admin'
    ],

    // Pagination settings
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    // File upload settings for supporting documents (PDFs, text files)
    'attachments' => [
        'max_size' => 2048, // 2MB in kilobytes
        'allowed_types' => [
            'application/pdf',
            'text/plain',
            'text/csv',
        ],
        'max_files' => 3,
    ],

    // Cache settings
    'cache' => [
        'ttl' => 3600, // 1 hour in seconds
        'prefix' => 'change_request_',
    ],

    // Notification settings
    'notifications' => [
        'mail' => [
            'enabled' => true,
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Database Change Request System'),
            ],
        ],
    ],
];
