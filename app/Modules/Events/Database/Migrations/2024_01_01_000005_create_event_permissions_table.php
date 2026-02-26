<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        // Insert permissions into Spatie's permission tables
        $permissions = [
            // Event permissions
            ['name' => 'view events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'create events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'edit events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'delete events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'publish events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'cancel events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'duplicate events', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'manage all events', 'guard_name' => 'web', 'module' => 'events'],
            
            // Category permissions
            ['name' => 'view categories', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'create categories', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'edit categories', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'delete categories', 'guard_name' => 'web', 'module' => 'events'],
            
            // Booking management permissions
            ['name' => 'view bookings', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'view own bookings', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'create bookings', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'cancel own bookings', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'manage all bookings', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'check-in attendees', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'export bookings', 'guard_name' => 'web', 'module' => 'events'],
            
            // Vendor management permissions
            ['name' => 'approve vendors', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'suspend vendors', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'view vendor reports', 'guard_name' => 'web', 'module' => 'events'],
            
            // Analytics permissions
            ['name' => 'view analytics', 'guard_name' => 'web', 'module' => 'events'],
            ['name' => 'export reports', 'guard_name' => 'web', 'module' => 'events'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Remove permissions
        DB::table('permissions')->whereIn('name', [
            'view events', 'create events', 'edit events', 'delete events',
            'publish events', 'cancel events', 'duplicate events', 'manage all events',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view bookings', 'view own bookings', 'create bookings', 'cancel own bookings',
            'manage all bookings', 'check-in attendees', 'export bookings',
            'approve vendors', 'suspend vendors', 'view vendor reports',
            'view analytics', 'export reports'
        ])->delete();
    }
};