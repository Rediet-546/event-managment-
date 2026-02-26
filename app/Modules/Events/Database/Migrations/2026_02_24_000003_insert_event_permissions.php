<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $permissions = [
            // Event permissions
            ['name' => 'view events', 'guard_name' => 'web'],
            ['name' => 'create events', 'guard_name' => 'web'],
            ['name' => 'edit events', 'guard_name' => 'web'],
            ['name' => 'delete events', 'guard_name' => 'web'],
            ['name' => 'publish events', 'guard_name' => 'web'],
            ['name' => 'cancel events', 'guard_name' => 'web'],
            ['name' => 'duplicate events', 'guard_name' => 'web'],
            ['name' => 'manage all events', 'guard_name' => 'web'],

            // Category permissions
            ['name' => 'view categories', 'guard_name' => 'web'],
            ['name' => 'create categories', 'guard_name' => 'web'],
            ['name' => 'edit categories', 'guard_name' => 'web'],
            ['name' => 'delete categories', 'guard_name' => 'web'],

            // Booking management permissions
            ['name' => 'view bookings', 'guard_name' => 'web'],
            ['name' => 'view own bookings', 'guard_name' => 'web'],
            ['name' => 'create bookings', 'guard_name' => 'web'],
            ['name' => 'cancel own bookings', 'guard_name' => 'web'],
            ['name' => 'manage all bookings', 'guard_name' => 'web'],
            ['name' => 'check-in attendees', 'guard_name' => 'web'],
            ['name' => 'export bookings', 'guard_name' => 'web'],

            // Vendor management permissions (legacy naming kept for compatibility)
            ['name' => 'approve vendors', 'guard_name' => 'web'],
            ['name' => 'suspend vendors', 'guard_name' => 'web'],
            ['name' => 'view vendor reports', 'guard_name' => 'web'],

            // Analytics permissions
            ['name' => 'view analytics', 'guard_name' => 'web'],
            ['name' => 'export reports', 'guard_name' => 'web'],
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
        if (!Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')->whereIn('name', [
            'view events', 'create events', 'edit events', 'delete events',
            'publish events', 'cancel events', 'duplicate events', 'manage all events',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view bookings', 'view own bookings', 'create bookings', 'cancel own bookings',
            'manage all bookings', 'check-in attendees', 'export bookings',
            'approve vendors', 'suspend vendors', 'view vendor reports',
            'view analytics', 'export reports',
        ])->delete();
    }
};

