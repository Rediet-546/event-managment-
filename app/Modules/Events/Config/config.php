<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Event Module Configuration
    |--------------------------------------------------------------------------
    */

    // Event statuses
    'statuses' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
    ],

    // Event types/categories defaults
    'default_category_color' => '#3498db',
    'default_category_icon' => 'fa-calendar',

    // Pagination settings
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    // Media settings
    'media' => [
        'max_size' => 2048, // KB
        'allowed_types' => ['jpeg', 'jpg', 'png', 'gif'],
        'path' => 'events',
        'thumbnail_size' => [
            'width' => 300,
            'height' => 200,
        ],
    ],

    // Cache settings
    'cache' => [
        'featured_events_ttl' => 3600, // seconds
        'categories_ttl' => 7200,
    ],

    // Map settings
    'maps' => [
        'default_latitude' => 40.7128,
        'default_longitude' => -74.0060,
        'default_zoom' => 12,
    ],

    // Date format settings
    'date_format' => 'Y-m-d H:i:s',
    'timezone' => 'UTC',
];