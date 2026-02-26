<?php

namespace App\Modules\Registration\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define gates
        Gate::define('access-creator-dashboard', function ($user) {
            return $user->isEventCreator() && $user->is_approved || $user->hasRole(['super-admin', 'admin']);
        });

        Gate::define('manage-users', function ($user) {
            return $user->hasRole(['super-admin', 'admin']);
        });

        Gate::define('approve-creators', function ($user) {
            return $user->hasRole(['super-admin', 'admin']);
        });

        Gate::define('view-analytics', function ($user) {
            return $user->hasRole(['super-admin', 'admin']) || 
                   ($user->isEventCreator() && $user->is_approved);
        });
    }
}