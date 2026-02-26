<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register Attendee Module
        if (class_exists(\Modules\Attendee\Providers\AttendeeServiceProvider::class)) {
            $this->app->register(\Modules\Attendee\Providers\AttendeeServiceProvider::class);
        }
    }

    public function boot()
    {
        // Load module migrations
        $attendeeMigrations = base_path('app/Modules/Attendee/Database/Migrations');
        if (is_dir($attendeeMigrations)) {
            $this->loadMigrationsFrom($attendeeMigrations);
        }
    }
}