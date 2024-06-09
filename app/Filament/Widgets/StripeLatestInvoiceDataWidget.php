<?php

namespace App\Filament\Widgets;

use App\Jobs\SendStripeInvoiceLinkJob;
use App\Models\StripeInvoice;
use App\Traits\HandlesInvoiceCreation;
use App\Traits\HandlesInvoiceStatus;
use App\Traits\ManagesChatwootMetadata; // Add this line
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class StripeLatestInvoiceDataWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use HandlesInvoiceCreation, HandlesInvoiceStatus, InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters, ManagesChatwootMetadata; // Updated line

    protected static string $view = 'filament.widgets.stripe-latest-invoice-data-widget';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    public $invoiceId;

    public array $invoice = [];

    #[Computed]
    public function getLatestInvoiceData()
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;

        Log::info('Fetching latest invoice data', ['contactId' => $contactId]);

        if (! $contactId) {
            Log::warning('Contact ID is not provided.');

            return [];
        }

        $invoice = StripeInvoice::latestForContact($contactId)
            ->active()
            ->first();

        if ($invoice) {
            Log::info('Latest invoice found', ['invoiceId' => $invoice->id]);
        } else {
            Log::warning('No invoice found for contact', ['contactId' => $contactId]);
        }

        return $invoice ? $invoice->toArray() : [];
    }

    public function sendStripeInvoiceLink()
    {
        $accountId = $this->filters['chatwootAccountId'] ?? null;
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $conversationId = $this->filters['chatwootConversationId'] ?? null;

        Log::info('Preparing to send Stripe invoice link', [
            'accountId' => $accountId,
            'contactId' => $contactId,
            'conversationId' => $conversationId,
        ]);

        if (! $this->invoice || ! $accountId || ! $contactId || ! $conversationId) {
            Log::error('Missing required filters for sending invoice link', [
                'invoiceId' => $this->invoice['id'] ?? null,
                'accountId' => $accountId,
                'contactId' => $contactId,
                'conversationId' => $conversationId,
            ]);

            return;
        }

        // Dispatch the job
        SendStripeInvoiceLinkJob::dispatch($this->invoice['id'], $accountId, $contactId, $conversationId);

        Log::info('Job dispatched for sending invoice link');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->invoice = $this->getLatestInvoiceData();

        $this->setChatwootMetadataFromFilters($this->filters);

        return $infolist
            ->state($this->invoice)
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
                                productId: $this->invoice['data']['lines']['data'][0]['price']['product'],
                                currency: $this->invoice['data']['lines']['data'][0]['price']['currency'],
                                priceId: $this->invoice['data']['lines']['data'][0]['price']['id'],
                                quantity: $this->invoice['data']['lines']['data'][0]['quantity'],
                            ))
                            ->action(function ($data) {
                                $this->createInvoice([$data]);
                            })
                            ->button()
                            ->outlined()
                            ->disabled(! $this->invoice)
                            ->color('primary'),
                        Action::make('sendStripeInvoiceLink')
                            ->color('warning')
                            ->disabled((! $this->invoice) || (empty($this->invoice['data']['hosted_invoice_url'])))
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
                            ->state(fn () => $this->invoice ? Arr::pluck($this->invoice['data']['lines']['data'], 'description') : null)
                            ->label('Usługi')
                            ->inlineLabel(),
                        TextEntry::make('total')
                            ->label('Suma')
                            ->placeholder('brak danych')
                            ->money(fn () => $this->invoice['data']['currency'], divideBy: 100)
                            ->badge()
                            ->inlineLabel()
                            ->color(fn () => $this->invoice['data']['paid'] ? 'success' : 'danger'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->placeholder('brak danych')
                            ->color(fn () => $this->getInvoiceStatusColor($this->invoice['status'] ?? null)) // Updated to use trait method for color
                            ->state(fn () => $this->getInvoiceStatusLabel($this->invoice['status'] ?? null)) // Updated to use trait method for label translation
                            ->inlineLabel()
                            ->badge(),
                    ]),
            ]);
    }
}
