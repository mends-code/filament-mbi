<?php

namespace App\Filament\Widgets;

use App\Models\StripeInvoice;
use App\Models\StripeCustomer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;
use Filament\Infolists\Components\Actions\Action;
use Filament\Forms\Components\Textarea;

class StripeLatestInvoiceWidget extends Widget implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.stripe-latest-invoice-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static bool $isLazy = true;

    #[Reactive]
    public ?array $filters = null;

    public function getLatestInvoice()
    {
        $invoice = StripeInvoice::latestForContact($this->filters['chatwootContactId']);
        $customer = StripeCustomer::findOrFail($invoice['customer_id']);

        if ($invoice) {
            return [
                'id' => $invoice->id,
                'data' => $invoice->data,
                'customer' => $customer,
            ];
        }

    }

    public function infolist(Infolist $infolist): Infolist
    {
        $latestInvoice = $this->getLatestInvoice();

        return $infolist
            ->state($latestInvoice)
            ->schema([
                Section::make('invoiceDetails')
                    ->heading('Szczegóły ostatniej faktury')
                    ->schema([
                        TextEntry::make('customer.id')
                            ->label('Identyfikator klienta')
                            ->inlineLabel()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('customer.data.name')
                            ->label('Identyfikator klienta')
                            ->inlineLabel(),
                        TextEntry::make('customer.data.email')
                            ->label('Adres email klienta')
                            ->inlineLabel(),
                        TextEntry::make('customer.data.phone')
                            ->label('Numer telefonu klienta')
                            ->inlineLabel(),
                        TextEntry::make('id')
                            ->label('Identyfikator faktury')
                            ->inlineLabel()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('data.created')
                            ->label('Utworzono')
                            ->inlineLabel()
                            ->since(),
                        TextEntry::make('data.total')
                            ->label('Suma')
                            ->money(fn() => $latestInvoice['data']['currency'], divideBy: 100)
                            ->badge()
                            ->inlineLabel()
                            ->color(fn() => $latestInvoice['data']['paid'] ? 'success' : 'danger'),
                        TextEntry::make('data.status')
                            ->label('Status')
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'gray',
                                'open' => 'info',
                                'paid' => 'success',
                                'uncollectible' => 'danger',
                                'void' => 'gray'
                            })
                            ->inlineLabel()
                            ->badge(),
                    ])
                    ->columns([
                        'sm' => 1,
                        'lg' => 2,
                    ]),
                Section::make('invoiceItems')
                    ->heading('Pozycje faktury')
                    ->schema([
                        RepeatableEntry::make('data.lines.data')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Usługa'),
                                TextEntry::make('price.unit_amount')
                                    ->money(fn() => $latestInvoice['data']['currency'], divideBy: 100)
                                    ->badge()
                                    ->label('Cena jednostkowa')
                                    ->color('gray'),
                                TextEntry::make('quantity')
                                    ->label('Ilość'),
                                TextEntry::make('amount')
                                    ->money(fn() => $latestInvoice['data']['currency'], divideBy: 100)
                                    ->badge()
                                    ->color('gray')
                                    ->label('Suma'),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 4,
                            ])
                            ->hiddenLabel(),
                    ])
            ]);
    }
}
