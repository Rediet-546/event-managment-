<?php

namespace App\Modules\Events\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Policies\EventPolicy;
use App\Modules\Events\Observers\EventObserver;
use App\Modules\Events\Repositories\EventRepository;
use App\Modules\Events\Repositories\EventRepositoryInterface;
use App\Modules\Events\Services\EventService;
use App\Modules\Events\Services\CapacityService;
use App\Modules\Events\Events\EventCreated;
use App\Modules\Events\Events\EventUpdated;
use App\Modules\Events\Events\EventDeleted;
use App\Modules\Events\Events\EventPublished;
use App\Modules\Events\Events\EventCancelled;
use App\Modules\Events\Events\EventViewed;
use App\Modules\Events\Events\EventCapacityReached;
use App\Modules\Events\Listeners\SendEventCreatedNotification;
use App\Modules\Events\Listeners\SendEventPublishedNotification;
use App\Modules\Events\Listeners\SendEventCancelledNotification;
use App\Modules\Events\Listeners\UpdateEventCache;
use App\Modules\Events\Listeners\UpdateEventStatistics;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    protected $listen = [
        // Event lifecycle events
        EventCreated::class => [
            SendEventCreatedNotification::class,
            UpdateEventCache::class . '@handleCreated',
        ],
        EventUpdated::class => [
            UpdateEventCache::class . '@handleUpdated',
        ],
        EventDeleted::class => [
            UpdateEventCache::class . '@handleDeleted',
        ],
        
        // Event status change events
        EventPublished::class => [
            SendEventPublishedNotification::class,
            UpdateEventCache::class . '@handlePublished',
        ],
        EventCancelled::class => [
            SendEventCancelledNotification::class,
            UpdateEventCache::class . '@handleCancelled',
        ],
        
        // Event interaction events
        EventViewed::class => [
            UpdateEventStatistics::class . '@handleViewed',
        ],
        EventCapacityReached::class => [
            UpdateEventStatistics::class . '@handleCapacityReached',
            UpdateEventCache::class . '@handleCapacityReached',
        ],
    ];

    /**
     * Model observers.
     */
    protected $observers = [
        Event::class => [EventObserver::class],
    ];

    /**
     * Model policies.
     */
    protected $policies = [
        Event::class => EventPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        
        // Register services
        $this->app->singleton(EventService::class, function ($app) {
            return new EventService(
                $app->make(EventRepositoryInterface::class)
            );
        });

        $this->app->singleton(CapacityService::class, function ($app) {
            return new CapacityService(
                $app->make(EventRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->registerPolicies();
        $this->registerObservers();

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        if (file_exists(__DIR__ . '/../Routes/admin.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        }

        // Load migrations (module-local)
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Views and translations
        $viewsPath = realpath(__DIR__ . '/../Resources/views') ?: __DIR__ . '/../Resources/views';
        $this->loadViewsFrom($viewsPath, 'events');

        $langPath = realpath(__DIR__ . '/../Resources/Lang') ?: __DIR__ . '/../Resources/Lang';
        $this->loadTranslationsFrom($langPath, 'events');

        if (config('app.debug')) {
            try {
                Log::debug('EventServiceProvider booted. view paths: ' . json_encode(app('view')->getFinder()->getPaths()));
                Log::debug('EventServiceProvider booted. view namespaces: ' . json_encode(app('view')->getFinder()->getHints()));
            } catch (\Throwable $e) {
                Log::debug('EventServiceProvider debug logging failed: ' . $e->getMessage());
            }
        }
    }
    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        foreach ($this->observers as $model => $observers) {
            foreach ($observers as $observer) {
                $model::observe($observer);
            }
        }
    }
}