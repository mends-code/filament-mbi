<?php

namespace App\Filament\Pages;

use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    #[Session]
    public ?array $filters = null;

    public function mount()
    {
        $this->js('document.addEventListener("DOMContentLoaded", () => $wire.dispatch("clear-dashboard-context"), { once: true }); console.log("clear-dashboard-context");');
        $this->js('window.addEventListener("message", event => $wire.dispatch("set-dashboard-context", { context: event.data }));console.log("set-dashboard-context")');
    }

    public function booted()
    {
        $this->js('$wire.on("get-dashboard-context", () => window.parent.postMessage("chatwoot-dashboard-app:fetch-info", "*"));console.log("get-dashboard-context")');
    }

    #[On('clear-dashboard-context')]
    public function clearDashboardContext()
    {
        $this->filters = null;
    }

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
