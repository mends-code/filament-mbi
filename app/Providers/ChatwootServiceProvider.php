<?php

namespace App\Providers;

use App\Services\ChatwootService;
use Illuminate\Support\ServiceProvider;

class ChatwootServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChatwootService::class, function ($app) {
            return new ChatwootService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
