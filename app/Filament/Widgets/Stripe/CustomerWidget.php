<?php

namespace App\Filament\Widgets\Stripe;

use App\Models\Stripe\Customer;
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
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class CustomerWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use CanPoll, InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.stripe.customer-widget';

    protected int|string|array $columnSpan = 1;

    #[Computed]
    public function getCustomerData()
    {
        $customer = Customer::latestForContact($this->filters['chatwootContactId'] ?? null)->first();

        if ($customer) {
            Log::info('Stripe customer found', ['customer' => $customer->toArray()]);
        } else {
            Log::warning('Stripe customer not found');
        }

        return $customer ? $customer->toArray() : [];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        Log::info('Generating infolist for Stripe customer widget');

        return $infolist
            ->state($this->getCustomerData())
            ->schema([
                Section::make('customerDetails')
                    ->heading('Obsługiwany klient Stripe')
                    ->headerActions([
                        Action::make('switchActiveStripeCustomer')
                            ->label('Zmień klienta')
                            ->outlined()
                            ->button()
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
                        TextEntry::make('data.currency')
                            ->label('Domyślna waluta')
                            ->placeholder('brak danych')
                            ->inlineLabel()
                            ->badge()
                            ->color('info'),
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
