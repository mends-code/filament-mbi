<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        Gate::define('viewPulse', function ($user) {
            // Fetch the comma-separated list of emails from the environment variable, and convert it to an array
            $adminEmails = explode(',', env('PULSE_ADMIN_EMAILS'));

            // Check if the user's email is in the list of admin emails
            return in_array($user->email, $adminEmails);
        });
    }
}
