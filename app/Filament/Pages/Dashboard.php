<?php

namespace App\Filament\Pages;

use App\HasSessionFilters;
use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HasSessionFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    public function mount()
    {
        $this->js('window.addEventListener("message", event => $wire.dispatch("set-dashboard-filters", { context: event.data }));console.log("set-dashboard-filters")');
    }

    #[On('set-dashboard-filters')]
    public function setDashboardFilters($context)
    {
        $contextData = json_decode($context)->data;

        Arr::set($this->filters, 'chatwootContactId', $contextData->contact->id ?? null);
        Arr::set($this->filters, 'chatwootConversationDisplayId', $contextData->conversation->id ?? null);
        Arr::set($this->filters, 'chatwootInboxId', $contextData->conversation->inbox_id ?? null);
        Arr::set($this->filters, 'chatwootAccountId', $contextData->conversation->account_id ?? null);
        Arr::set($this->filters, 'chatwootCurrentAgentId', $contextData->currentAgent->id ?? null);
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

                    $customer = StripeCustomer::latestForContact($this->filters['chatwootContactId'] ?? null)->first() ?? null;

                    $logContext = [
                        'class' => __CLASS__,
                        'method' => __METHOD__,
                        'action' => 'createInvoice',
                        'contactId' => $this->filters['chatwootContactId'],
                        'priceId' => $data['priceId'],
                        'customerId' => $customer->id,
                        'timestamp' => now(),
                    ];

                    Log::info('Dispatching CreateStripeInvoiceJob', $logContext);

                    CreateStripeInvoiceJob::dispatch($this->filters['chatwootContactId'], $data['priceId'], $customer->id);
                }),

            Action::make('makeAppointment')->color('gray')->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
            Action::make('sendEmail')->color('gray')->label('Wyślij email')->icon('heroicon-o-envelope')->tooltip('wkrótce'),
            Action::make('sendSMS')->color('gray')->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text')->tooltip('wkrótce'),
        ];
    }
}
