<?php

namespace App\Filament\Pages;

use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HasFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    #[On('set-dashboard-context')]
    public function setDashboardContext($context)
    {
        $contextData = json_decode($context)->data;

        $customer = StripeCustomer::latestForContact($contextData->contact->id)->first();

        $this->filters = [
            'chatwootContactId' => $contextData->contact->id ?? null,
            'chatwootConversationDisplayId' => $contextData->conversation->id ?? null,
            'chatwootInboxId' => $contextData->conversation->inbox_id ?? null,
            'chatwootAccountId' => $contextData->conversation->account_id ?? null,
            'chatwootCurrentAgentId' => $contextData->currentAgent->id ?? null,
            'stripeCustomerId' => $customer ? $customer->id : null,
        ];

        Log::info('Dashboard context set', [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'filters' => $this->filters,
            'timestamp' => now(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createInvoice')
                ->modal()
                ->label('Wystaw fakturę')
                ->icon('heroicon-s-document-plus')
                ->form([
                    Radio::make('priceId')
                        ->label('Wybierz usługę')
                        ->options(function () {
                            $prices = StripePrice::active()->get();
                            $options = [];
                            foreach ($prices as $price) {
                                $description = $price->product->data['name'].' - '.($price->data['unit_amount'] / 100).' '.strtoupper($price->data['currency']);
                                $options[$price->id] = $description;
                            }

                            return $options;
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $logContext = [
                        'class' => __CLASS__,
                        'method' => __METHOD__,
                        'action' => 'createInvoice',
                        'contactId' => $this->filters['chatwootContactId'],
                        'priceId' => $data['priceId'],
                        'customerId' => $this->filters['stripeCustomerId'],
                        'timestamp' => now(),
                    ];

                    Log::info('Dispatching CreateStripeInvoiceJob', $logContext);

                    CreateStripeInvoiceJob::dispatch($this->filters['chatwootContactId'], $data['priceId'], $this->filters['stripeCustomerId']);
                }),

            Action::make('makeAppointment')->color('gray')->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
            Action::make('sendEmail')->color('gray')->label('Wyślij email')->icon('heroicon-o-envelope')->tooltip('wkrótce'),
            Action::make('sendSMS')->color('gray')->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text')->tooltip('wkrótce'),
        ];
    }
}
