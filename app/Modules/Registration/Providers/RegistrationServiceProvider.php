<?php

namespace App\Modules\Registration\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use App\Modules\Registration\Repositories\UserRepository;
use App\Modules\Registration\Repositories\UserRepositoryInterface;
use App\Modules\Registration\Services\AuthService;
use App\Modules\Registration\Services\UserService;
use App\Modules\Registration\Console\Commands\CleanUnverifiedUsers;
use App\Modules\Registration\Console\Commands\SendPendingReminders;

class RegistrationServiceProvider extends ServiceProvider
{
    /**
     * All module namespaces
     */
    protected $moduleNamespace = 'App\Modules\Registration\Http\Controllers';
    
    /**
     * Module name for translations and views
     */
    protected $moduleName = 'registration';
    
    /**
     * Module path
     */
    protected $modulePath = __DIR__ . '/..';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register module configurations
        $this->registerConfig();
        
        // Register repositories
        $this->registerRepositories();
        
        // Register services
        $this->registerServices();
        
        // Register module dependencies
        $this->registerModuleDependencies();
        
        // Register commands
        $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load module dependencies
        $this->loadMigrations();
        $this->loadRoutes();
        $this->loadViews();
        $this->loadTranslations();
        
        // Register blade directives
        $this->registerBladeDirectives();
        
        // Register view composers
        $this->registerViewComposers();
        
        // Register event listeners
        $this->registerEventListeners();
        
        // Register permissions
        $this->registerPermissions();
        
        // Register middleware
        $this->registerMiddleware();
        
        // Register policies
        $this->registerPolicies();
        
        // Extend Laravel's auth
        $this->extendAuth();
        
        // Publish assets (for module assets like images, css, js)
        $this->publishAssets();
    }

    /**
     * Register module configuration
     */
    private function registerConfig(): void
    {
        $configPath = $this->modulePath . '/Config/config.php';
        
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'registration');
        }
        
        // Publish config to main application
        $this->publishes([
            $configPath => config_path('registration.php'),
        ], 'registration-config');
    }

    /**
     * Register repositories
     */
    private function registerRepositories(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /**
     * Register services
     */
    private function registerServices(): void
    {
        // AuthService as singleton
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(UserRepositoryInterface::class)
            );
        });

        // UserService as singleton
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class)
            );
        });

        // Bind interfaces to implementations
        $this->app->bind('auth.service', AuthService::class);
        $this->app->bind('user.service', UserService::class);
    }

    /**
     * Register module dependencies (migrations, routes, views, translations)
     */
    private function registerModuleDependencies(): void
    {
        // Register the module in the modules list
        $this->app['config']->set('modules.registration', [
            'enabled' => true,
            'name' => 'Registration',
            'version' => '1.0.0',
            'provider' => self::class,
        ]);
    }

    /**
     * Register console commands
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanUnverifiedUsers::class,
                SendPendingReminders::class,
            ]);
        }
    }

    /**
     * Load module migrations
     */
    private function loadMigrations(): void
    {
        $migrationsPath = $this->modulePath . '/Database/Migrations';
        
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Load module routes
     */
    private function loadRoutes(): void
    {
        // Web routes
        $webRoutesPath = $this->modulePath . '/Routes/web.php';
        if (file_exists($webRoutesPath)) {
            Route::middleware('web')
                ->namespace($this->moduleNamespace)
                ->group($webRoutesPath);
        }

        // API routes
        $apiRoutesPath = $this->modulePath . '/Routes/api.php';
        if (file_exists($apiRoutesPath)) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->moduleNamespace)
                ->group($apiRoutesPath);
        }

        // Auth routes (additional auth-specific routes)
        $authRoutesPath = $this->modulePath . '/Routes/auth.php';
        if (file_exists($authRoutesPath)) {
            Route::middleware('web')
                ->namespace($this->moduleNamespace)
                ->group($authRoutesPath);
        }
    }

    /**
     * Load module views
     */
    private function loadViews(): void
    {
        $viewsPath = $this->modulePath . '/Resources/views';
        
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->moduleName);
        }

        // Publish views for customization
        $this->publishes([
            $viewsPath => resource_path('views/vendor/' . $this->moduleName),
        ], 'registration-views');
    }

    /**
     * Load module translations
     */
    private function loadTranslations(): void
    {
        $translationsPath = $this->modulePath . '/Resources/lang';
        
        if (is_dir($translationsPath)) {
            $this->loadTranslationsFrom($translationsPath, $this->moduleName);
        }

        // Publish translations
        $this->publishes([
            $translationsPath => resource_path('lang/vendor/' . $this->moduleName),
        ], 'registration-translations');
    }

    /**
     * Register custom Blade directives
     */
    private function registerBladeDirectives(): void
    {
        // Check if user is attendee
        Blade::if('attendee', function () {
            return auth()->check() && auth()->user()->isAttendee();
        });

        // Check if user is event creator
        Blade::if('creator', function () {
            return auth()->check() && auth()->user()->isEventCreator();
        });

        // Check if creator is approved
        Blade::if('approvedcreator', function () {
            return auth()->check() && auth()->user()->isApprovedCreator();
        });

        // Check if user is admin (including super-admin)
        Blade::if('admin', function () {
            return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'));
        });

        // Check if user is super admin
        Blade::if('superadmin', function () {
            return auth()->check() && auth()->user()->hasRole('super-admin');
        });

        // Display user's dashboard URL
        Blade::directive('dashboardurl', function () {
            return "<?php echo auth()->check() ? auth()->user()->dashboard_url : '#'; ?>";
        });

        // Display user type badge
        Blade::directive('usertypebadge', function ($user = null) {
            $user = $user ?: 'auth()->user()';
            return "<?php echo App\Modules\Registration\helpers\user_type_badge($user->user_type ?? 'unknown'); ?>";
        });
    }

    /**
     * Register view composers
     */
    private function registerViewComposers(): void
    {
        // Share auth status with all views
        View::composer('*', function ($view) {
            $view->with('authUser', auth()->user());
        });

        // Share registration settings with specific views
        View::composer(['registration::login', 'registration::register'], function ($view) {
            $view->with('registrationSettings', config('registration', []));
        });

        // Share dashboard data with dashboard views
        View::composer(['registration::attendee.dashboard', 'registration::creator.dashboard'], function ($view) {
            if (auth()->check()) {
                $userService = app(UserService::class);
                $dashboardData = $userService->getDashboardData(auth()->user());
                $view->with('dashboardData', $dashboardData);
            }
        });
    }

    /**
     * Register event listeners
     */
    private function registerEventListeners(): void
    {
        // Listen for user login events
        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            // Log the login activity
            activity()
                ->performedOn($event->user)
                ->withProperties(['ip' => request()->ip()])
                ->log('User logged in');
        });

        // Listen for user logout events
        Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                activity()
                    ->performedOn($event->user)
                    ->log('User logged out');
            }
        });

        // Listen for user registration events
        Event::listen(\Illuminate\Auth\Events\Registered::class, function ($event) {
            activity()
                ->performedOn($event->user)
                ->withProperties(['user_type' => $event->user->user_type])
                ->log('User registered');
        });

        // Listen for password reset events
        Event::listen(\Illuminate\Auth\Events\PasswordReset::class, function ($event) {
            activity()
                ->performedOn($event->user)
                ->log('Password reset');
        });

        // Listen for email verification events
        Event::listen(\Illuminate\Auth\Events\Verified::class, function ($event) {
            activity()
                ->performedOn($event->user)
                ->log('Email verified');
        });

        // Custom event for creator approval
        Event::listen('creator.approved', function ($user) {
            activity()
                ->performedOn($user)
                ->log('Creator account approved');
        });

        // Custom event for creator rejection
        Event::listen('creator.rejected', function ($user) {
            activity()
                ->performedOn($user)
                ->log('Creator account rejected');
        });
    }

    /**
     * Register module permissions
     */
    private function registerPermissions(): void
    {
        // Only register if Spatie Permission is installed
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            $permissions = [
                // User management
                'view users',
                'create users',
                'edit users',
                'delete users',
                'manage user roles',
                
                // Creator approval
                'approve creators',
                'reject creators',
                'view pending creators',
            ];

            foreach ($permissions as $permission) {
                try {
                    \Spatie\Permission\Models\Permission::firstOrCreate(
                        ['name' => $permission, 'guard_name' => 'web']
                    );
                } catch (\Exception $e) {
                    // Permission already exists or table not ready
                }
            }
        }
    }

    /**
     * Register module middleware
     */
    private function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Register middleware aliases
        $router->aliasMiddleware('creator.approved', 
            \App\Modules\Registration\Http\Middleware\ApprovedCreatorMiddleware::class);
        
        $router->aliasMiddleware('registration', 
            \App\Modules\Registration\Http\Middleware\RegistrationMiddleware::class);
        
        $router->aliasMiddleware('registration.age', 
            \App\Modules\Registration\Http\Middleware\RegistrationMiddleware::class . ':age-verify');
        
        $router->aliasMiddleware('registration.attempt', 
            \App\Modules\Registration\Http\Middleware\RegistrationMiddleware::class . ':attempt');
    }

    /**
     * Register module policies
     */
    private function registerPolicies(): void
    {
        $policies = [
            \App\Modules\Registration\Models\User::class => \App\Modules\Registration\Policies\UserPolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            try {
                $this->app[\Illuminate\Contracts\Auth\Access\Gate::class]->policy($model, $policy);
            } catch (\Exception $e) {
                // Policy registration failed
            }
        }
    }

    /**
     * Extend Laravel's authentication
     */
    private function extendAuth(): void
    {
        $this->app['auth']->provider('registration', function ($app, $config) {
            return new \Illuminate\Auth\EloquentUserProvider(
                $app['hash'],
                \App\Modules\Registration\Models\User::class
            );
        });
    }

    /**
     * Publish module assets
     */
    private function publishAssets(): void
    {
        $this->publishes([
            $this->modulePath . '/Resources/assets' => public_path('vendor/registration'),
        ], 'registration-assets');
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            AuthService::class,
            UserService::class,
            UserRepositoryInterface::class,
            'auth.service',
            'user.service',
        ];
    }
}