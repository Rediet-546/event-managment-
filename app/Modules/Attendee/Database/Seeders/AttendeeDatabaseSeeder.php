<?php

namespace Modules\Attendee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attendee\Models\AttendeeSetting;
use Modules\Attendee\Models\TicketType;

class AttendeeDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create default settings
        AttendeeSetting::set('booking_prefix', 'BKG', 'text', 'general');
        AttendeeSetting::set('max_per_order', 10, 'number', 'general');
        AttendeeSetting::set('expiry_hours', 24, 'number', 'general');
        AttendeeSetting::set('cancellation_cutoff', 48, 'number', 'general');
        AttendeeSetting::set('currency', 'USD', 'text', 'payment');
        AttendeeSetting::set('tax_rate', 0, 'number', 'payment');
        AttendeeSetting::set('service_fee', 0, 'number', 'payment');
        AttendeeSetting::set('ticket_prefix', 'TIC', 'text', 'ticket');
        AttendeeSetting::set('qr_size', 200, 'number', 'ticket');
        
        // Create sample ticket types
        TicketType::create([
            'name' => 'General Admission',
            'description' => 'Regular entry ticket',
            'price' => 25.00,
            'quantity_available' => 100,
            'max_per_order' => 5,
            'min_per_order' => 1,
            'status' => 'active'
        ]);
        
        TicketType::create([
            'name' => 'VIP Pass',
            'description' => 'VIP access with perks',
            'price' => 75.00,
            'quantity_available' => 20,
            'max_per_order' => 2,
            'min_per_order' => 1,
            'status' => 'active'
        ]);
        
        TicketType::create([
            'name' => 'Early Bird',
            'description' => 'Discounted early registration',
            'price' => 20.00,
            'quantity_available' => 50,
            'max_per_order' => 4,
            'min_per_order' => 1,
            'sale_start_date' => now(),
            'sale_end_date' => now()->addDays(30),
            'status' => 'active'
        ]);
    }
}