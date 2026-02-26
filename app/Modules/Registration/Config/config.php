<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Registration Module Configuration
    |--------------------------------------------------------------------------
    */

    'enabled' => true,
    
    'version' => '1.0.0',
    
    'name' => 'User Registration & Authentication',

    'user_types' => [
        'attendee' => [
            'name' => 'Event Attendee',
            'default_role' => 'attendee',
            'requires_approval' => false,
            'fields' => ['first_name', 'last_name', 'email', 'password', 'age'],
            'max_age' => 120,
        ],
        'event_creator' => [
            'name' => 'Event Creator',
            'default_role' => 'event-creator',
            'requires_approval' => true,
            'fields' => [
                'first_name', 'last_name', 'email', 'password', 
                'phone', 'organization_name', 'tax_id'
            ],
            'max_events' => 50,
        ],
    ],

    'validation' => [
        'minimum_age' => 18,
        'password_min_length' => 8,
        'password_requires_mixed_case' => true,
        'password_requires_numbers' => true,
        'password_requires_symbols' => true,
        'username_unique' => true,
        'email_unique' => true,
        'email_verification_required' => true,
    ],

    'login' => [
        'max_attempts' => 5,
        'lockout_duration' => 15, // minutes
        'remember_me_duration' => 43200, // minutes (30 days)
        'throttle' => true,
    ],

    'registration' => [
        'enabled' => true,
        'allow_attendee_registration' => true,
        'allow_creator_registration' => true,
        'auto_login_after_registration' => true,
        'terms_required' => true,
        'privacy_policy_required' => true,
        'email_verification_required' => true,
    ],

    'profile' => [
        'allow_avatar_upload' => true,
        'max_avatar_size' => 2048, // KB
        'allowed_avatar_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'avatar_dimensions' => [300, 300],
        'allow_social_links' => true,
        'allow_bio' => true,
        'max_bio_length' => 500,
    ],

    'notifications' => [
        'send_welcome_email' => true,
        'send_verification_email' => true,
        'send_password_reset_email' => true,
        'send_approval_email' => true,
        'send_rejection_email' => true,
        'send_pending_reminder' => true,
        'pending_reminder_days' => 3,
    ],

    'social_login' => [
        'enabled' => false,
        'providers' => ['google', 'facebook', 'twitter', 'github'],
        'allow_email_matching' => true,
        'auto_create_users' => true,
    ],

    'two_factor' => [
        'enabled' => false,
        'required_for_roles' => ['admin', 'super-admin'],
        'methods' => ['email', 'sms', 'authenticator'],
    ],

    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'inactive_timeout' => 30, // minutes
    ],

    'dashboard_routes' => [
        'attendee' => 'attendee.dashboard',
        'event_creator' => 'creator.dashboard',
        'admin' => 'admin.dashboard',
        'super_admin' => 'admin.dashboard',
    ],

    'account_deletion' => [
        'enabled' => true,
        'require_password_confirmation' => true,
        'soft_delete' => true,
        'retention_period' => 30, // days before permanent delete
        'anonymize_data' => true,
    ],
];