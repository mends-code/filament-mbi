<?php

namespace App\Filament\Widgets;

use App\Models\StripeInvoice;
use App\Services\ChatwootService;
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
use Illuminate\Support\Facades\Log;
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

        Log::info('Fetching latest invoice data', ['contactId' => $contactId]);

        if (! $contactId) {
            Log::warning('Contact ID is not provided.');

            return [];
        }

        $invoice = StripeInvoice::latestForContact($contactId)->first();

        if ($invoice) {
            Log::info('Latest invoice found', ['invoiceId' => $invoice->id]);
        } else {
            Log::warning('No invoice found for contact', ['contactId' => $contactId]);
        }

        return $invoice ? $invoice->toArray() : [];
    }

    public function sendStripeInvoiceLink(ChatwootService $chatwootService)
    {
        $accountId = $this->filters['chatwootAccountId'] ?? null;
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $conversationId = $this->filters['chatwootConversationDisplayId'] ?? null;

        Log::info('Preparing to send Stripe invoice link', [
            'accountId' => $accountId,
            'contactId' => $contactId,
            'conversationId' => $conversationId,
        ]);

        if (! $accountId || ! $contactId || ! $conversationId) {
            Log::error('Missing required filters for sending invoice link', [
                'accountId' => $accountId,
                'contactId' => $contactId,
                'conversationId' => $conversationId,
            ]);

            // Handle error
            return;
        }

        $invoice = StripeInvoice::latestForContact($contactId)->first();

        if (! $invoice) {
            Log::error('No invoice found for contact', ['contactId' => $contactId]);

            // Handle error
            return;
        }

        // Build the messages
        $messages = [
            'Link do faktury:',
            $invoice->data['hosted_invoice_url'],
            'Łączna kwota do zapłaty:',
            ($invoice->data['total'] / 100).' '.strtoupper($invoice->data['currency']),
            'Prosimy o płatność w terminie.',
        ];

        Log::info('Sending messages to Chatwoot', ['messages' => $messages]);

        // Send the messages
        $responses = $chatwootService->sendMessages($accountId, $conversationId, $messages);

        // Check for errors in the responses
        foreach ($responses as $response) {
            if (isset($response['error'])) {
                Log::error('Error sending message to Chatwoot', ['response' => $response]);

                // Handle error
                return;
            }
        }

        Log::info('Messages sent successfully');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $invoice = $this->getLatestInvoiceData();

        Log::info('Building infolist', ['invoice' => $invoice]);

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
                            ->tooltip('w trakcie testów')
                            ->action('sendStripeInvoiceLink'),
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
