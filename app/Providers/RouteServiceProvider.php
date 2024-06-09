<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->mapRoutes();
    }

    /**
     * Define the routes for the application.
     */
    protected function mapRoutes(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapWebhookRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "webhook" routes for the application.
     */
    protected function mapWebhookRoutes(): void
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/webhook.php'));
    }
}
