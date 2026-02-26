<?php

if (!function_exists('attendee_setting')) {
    function attendee_setting($key, $default = null)
    {
        return \Modules\Attendee\Models\AttendeeSetting::get($key, $default);
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        $currency = attendee_setting('currency', 'USD');
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        
        return $symbol . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('booking_status_badge')) {
    function booking_status_badge($status)
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'info',
            'expired' => 'secondary'
        ];
        
        $color = $colors[$status] ?? 'secondary';
        
        return '<span class="badge badge-' . $color . '">' . ucfirst($status) . '</span>';
    }
}