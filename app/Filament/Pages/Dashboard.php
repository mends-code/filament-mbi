<?php

namespace App\Filament\Pages;

use App\Models\StripeCustomer;
use App\Models\StripePrice;
use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    #[Session(key: 'dashboard-filters')]
    public ?array $filters = null;

    #[On('set-chatwoot-context')]
    public function setChatwootContext($context)
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
                    // Handle the form submission here, for example:
                    $this->createInvoice($this->filters['chatwootContactId'], $data['priceId'], $this->filters['stripeCustomerId']);
                }),

            Action::make('makeAppointment')->color('gray')->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
            Action::make('sendEmail')->color('gray')->label('Wyślij email')->icon('heroicon-o-envelope')->tooltip('wkrótce'),
            Action::make('sendSMS')->color('gray')->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text')->tooltip('wkrótce'),
        ];
    }

    private function createInvoice($contactId, $priceId, $customerId)
    {
        $stripeService = app(StripeService::class);

        try {

            return $stripeService->createInvoice($contactId, $priceId, $customerId);

            // Optionally, you can handle any post-invoice creation logic here
            // For example, you might want to refresh the dashboard or display a success message

        } catch (\Exception $e) {
            // Handle any errors that may occur during the invoice creation process
        }
    }
}
