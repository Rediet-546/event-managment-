<?php

if (!function_exists('event_status_badge')) {
    /**
     * Get status badge HTML for event status.
     */
    function event_status_badge(string $status): string
    {
        $colors = [
            'draft' => 'gray',
            'published' => 'green',
            'cancelled' => 'red',
            'completed' => 'blue',
        ];

        $color = $colors[$status] ?? 'gray';
        
        return "<span class='px-2 py-1 text-xs rounded-full bg-{$color}-100 text-{$color}-800'>{$status}</span>";
    }
}

if (!function_exists('format_event_price')) {
    /**
     * Format event price with currency.
     */
    function format_event_price(?float $price, string $currency = 'USD', bool $isFree = false): string
    {
        if ($isFree || $price <= 0) {
            return 'Free';
        }
        
        return number_format($price, 2) . ' ' . strtoupper($currency);
    }
}

if (!function_exists('get_event_duration')) {
    /**
     * Get human-readable event duration.
     */
    function get_event_duration(Carbon\Carbon $start, Carbon\Carbon $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->format('M d, Y') . ' • ' . $start->format('g:i A') . ' - ' . $end->format('g:i A');
        }
        
        return $start->format('M d, Y g:i A') . ' - ' . $end->format('M d, Y g:i A');
    }
}