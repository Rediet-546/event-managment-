<?php

return [
    'name' => 'Attendee Module',
    'version' => '1.0.0',
    
    'booking' => [
        'prefix' => 'BKG',
        'expiry_hours' => 24,
        'max_per_order' => 10,
        'cancellation_cutoff' => 48,
    ],
    
    'payment' => [
        'currency' => 'USD',
        'tax_rate' => 0,
        'service_fee' => 0,
        'stripe_enabled' => false,
        'paypal_enabled' => false,
    ],
    
    'ticket' => [
        'prefix' => 'TIC',
        'qr_size' => 200,
        'format' => 'pdf',
    ],
];