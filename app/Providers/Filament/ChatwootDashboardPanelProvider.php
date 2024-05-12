<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ChatwootDashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('chatwoot-dashboard')
            ->path('chatwoot-dashboard')
            ->colors([
                'primary' => '#1f93ff',
            ])
            ->brandName('Chatwoot Dashboard')
            ->topNavigation()
            ->brandLogo(asset('images/chatwoot-square.svg'))
            ->discoverResources(in: app_path('Filament/ChatwootDashboard/Resources'), for: 'App\\Filament\\ChatwootDashboard\\Resources')
            ->discoverPages(in: app_path('Filament/ChatwootDashboard/Pages'), for: 'App\\Filament\\ChatwootDashboard\\Pages')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/ChatwootDashboard/Widgets'), for: 'App\\Filament\\ChatwootDashboard\\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
