<?php

namespace Modules\Attendee\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AttendeeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/attendee.php', 'attendee');
    }

    public function boot()
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->loadMigrations();
    }

    protected function loadRoutes()
    {
        // Web Routes
        if (file_exists(__DIR__.'/../Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__.'/../Routes/web.php');
        }
    }

    protected function loadViews()
    {
        if (is_dir(__DIR__.'/../Resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../Resources/views', 'attendee');
        }
    }

    protected function loadMigrations()
    {
        if (is_dir(__DIR__.'/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        }
    }
}