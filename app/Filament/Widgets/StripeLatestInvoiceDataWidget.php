<?php

namespace App\Filament\Widgets;

use App\Models\StripeInvoice;
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
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;

class StripeLatestInvoiceDataWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.stripe-latest-invoice-data-widget';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    #[Reactive]
    public ?array $filters = null;

    public function getLatestInvoiceData()
    {

        $contactId = $this->filters['chatwootContactId'] ?? null;

        if (! $contactId) {
            return [];
        }

        $invoice = StripeInvoice::latestForContact($contactId)->first();

        return $invoice ? $invoice->toArray() : [];

    }

    public function infolist(Infolist $infolist): Infolist
    {
        $invoice = $this->getLatestInvoiceData();

        return $infolist
            ->state($invoice)
            ->schema([
                Section::make('invoiceDetails')
                    ->heading('Ostatnia faktura Stripe')
                    ->headerActions([
                        Action::make('sendStripeInvoiceLink')
                            ->label('Wyślij link')
                            ->link()
                            ->icon('heroicon-o-link')
                            ->tooltip('wkrótce'),
                    ])
                    ->schema([
                        TextEntry::make('id')
                            ->label('Identyfikator faktury')
                            ->inlineLabel()
                            ->placeholder('N/A')
                            ->badge(),
                        TextEntry::make('data.created')
                            ->label('Utworzono')
                            ->placeholder('brak danych')
                            ->inlineLabel()
                            ->since()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('data.total')
                            ->label('Suma')
                            ->placeholder('brak danych')
                            ->money(fn () => $invoice['data']['currency'], divideBy: 100)
                            ->badge()
                            ->inlineLabel()
                            ->color(fn () => $invoice['data']['paid'] ? 'success' : 'danger'),
                        TextEntry::make('data.status')
                            ->label('Status')
                            ->placeholder('brak danych')
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'open' => 'info',
                                'paid' => 'success',
                                'uncollectible' => 'danger',
                                'void' => 'gray'
                            })
                            ->inlineLabel()
                            ->badge(),
                    ]),
            ]);
    }
}
