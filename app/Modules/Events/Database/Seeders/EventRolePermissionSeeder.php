<?php

namespace App\Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class EventRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Get all permissions
        $permissions = Permission::all();

        // Super Admin gets all permissions
        $superAdmin->syncPermissions($permissions);

        // Admin permissions
        $adminPermissions = Permission::whereIn('name', [
            'view events', 'create events', 'edit events', 'delete events',
            'publish events', 'cancel events', 'duplicate events', 'manage all events',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view bookings', 'manage all bookings', 'check-in attendees', 'export bookings',
            'approve vendors', 'suspend vendors', 'view vendor reports',
            'view analytics', 'export reports'
        ])->get();
        $admin->syncPermissions($adminPermissions);

        // Vendor permissions
        $vendorPermissions = Permission::whereIn('name', [
            'view events', 'create events', 'edit events', 
            'view own bookings', 'export bookings',
            'view analytics'
        ])->get();
        $vendor->syncPermissions($vendorPermissions);

        // Regular user permissions
        $userPermissions = Permission::whereIn('name', [
            'view events', 'view own bookings', 'create bookings', 'cancel own bookings'
        ])->get();
        $user->syncPermissions($userPermissions);

        // Assign default roles to existing users (optional)
        $this->assignDefaultRoles();
    }

    protected function assignDefaultRoles(): void
    {
        // Assign admin role to users with email admin@example.com (for testing)
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // Assign vendor role to users with email vendor@example.com (for testing)
        $vendorUser = User::where('email', 'vendor@example.com')->first();
        if ($vendorUser && !$vendorUser->hasRole('vendor')) {
            $vendorUser->assignRole('vendor');
        }

        // Assign user role to all other users
        User::whereDoesntHave('roles')->each(function ($user) {
            $user->assignRole('user');
        });
    }
}