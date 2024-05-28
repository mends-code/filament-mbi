<?php

namespace App\Filament\Widgets;

use App\Models\StripeCustomer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class StripeCustomerDataWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.stripe-customer-data-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    public function getCustomerData()
    {
        $customerId = $this->filters['stripeCustomerId'] ?? null;

        if (! $customerId) {
            return [];
        }

        $customer = StripeCustomer::find($customerId);

        return $customer ? $customer->toArray() : [];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->getCustomerData())
            ->schema([
                Section::make('customerDetails')
                    ->heading('Obsługiwany klient Stripe')
                    ->headerActions([
                        Action::make('switchActiveStripeCustomer')
                            ->label('Zmień klienta')
                            ->link()
                            ->tooltip('wkrótce')
                            ->icon('heroicon-o-credit-card'),
                    ])
                    ->schema([
                        TextEntry::make('id')
                            ->label('Identyfikator klienta')
                            ->placeholder('N/A')
                            ->inlineLabel()
                            ->badge(),
                        TextEntry::make('data.created')
                            ->label('Utworzono')
                            ->placeholder('brak danych')
                            ->inlineLabel()
                            ->since()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('data.name')
                            ->label('Imię i nazwisko')
                            ->placeholder('brak danych')
                            ->inlineLabel(),
                        TextEntry::make('data.email')
                            ->label('Adres email')
                            ->placeholder('brak danych')
                            ->inlineLabel(),
                    ]),
            ]);
    }
}
