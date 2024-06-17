<?php

namespace App\Filament\Widgets\Stripe;

use App\Traits\HandlesStripeInvoice;
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
use Illuminate\Support\Arr;

class LatestInvoiceWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use CanPoll, HandlesStripeInvoice, InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.stripe.latest-invoice-widget';

    protected int|string|array $columnSpan = 1;

    public function infolist(Infolist $infolist): Infolist
    {

        $this->fetchLatestInvoiceData();

        $invoice = $this->invoice->toArray();

        return $infolist
            ->state($invoice)
            ->schema([
                Section::make('invoiceDetails')
                    ->heading('Ostatnia faktura Stripe')
                    ->headerActions([
                        Action::make('cloneInvoice')
                            ->label('Skopiuj')
                            ->modalHeading('Skopiuj fakturę')
                            ->modalDescription('Wybierz walutę, konkretną usługę oraz jej cenę. W przypadku płatności za kilka takich samych usług możesz ustawić żądaną ilość.')
                            ->icon('heroicon-o-clipboard-document')
                            ->form(fn () => $this->getInvoiceFormSchema(
                                productId: $invoice['data']['lines']['data'][0]['price']['product'],
                                currency: $invoice['data']['lines']['data'][0]['price']['currency'],
                                priceId: $invoice['data']['lines']['data'][0]['price']['id'],
                                quantity: $invoice['data']['lines']['data'][0]['quantity'],
                            ))
                            ->action(function ($data) {
                                $this->createInvoice([$data]);
                            })
                            ->button()
                            ->outlined()
                            ->disabled(! $invoice)
                            ->color('primary'),
                        Action::make('sendStripeInvoiceLink')
                            ->color('warning')
                            ->disabled((! $invoice) || (empty($invoice['data']['hosted_invoice_url'])))
                            ->label('Wyślij link')
                            ->outlined()
                            ->button()
                            ->icon('heroicon-o-link')
                            ->requiresConfirmation()
                            ->action(fn () => $this->sendStripeInvoiceLink()),
                    ])
                    ->schema([
                        TextEntry::make('id')
                            ->label('Identyfikator faktury')
                            ->inlineLabel()
                            ->placeholder('N/A')
                            ->badge(),
                        TextEntry::make('created')
                            ->label('Utworzono')
                            ->placeholder('brak danych')
                            ->inlineLabel()
                            ->since()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('description')
                            ->placeholder('brak danych')
                            ->state(fn () => $invoice ? Arr::pluck($invoice['data']['lines']['data'], 'description') : null)
                            ->label('Usługi')
                            ->inlineLabel(),
                        TextEntry::make('total')
                            ->label('Suma')
                            ->placeholder('brak danych')
                            ->money(fn () => $invoice['data']['currency'], divideBy: 100)
                            ->badge()
                            ->inlineLabel()
                            ->color(fn () => $invoice['data']['paid'] ? 'success' : 'danger'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->placeholder('brak danych')
                            ->color(fn () => $this->getInvoiceStatusColor($invoice['status'] ?? null)) // Updated to use trait method for color
                            ->state(fn () => $this->getInvoiceStatusLabel($invoice['status'] ?? null)) // Updated to use trait method for label translation
                            ->inlineLabel()
                            ->badge(),
                    ]),
            ]);
    }
}
