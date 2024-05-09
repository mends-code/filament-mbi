<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Filament\Support\View\Components\Modal;
use Filament\Tables\Table;

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
        if (env('LARAVEL_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        Modal::closedByClickingAway(false);
        Modal::closeButton(false);
        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationPageOptions([5, 10, 25])
                ->defaultPaginationPageOption(5)
                ->persistFiltersInSession();
        });
    }
}
