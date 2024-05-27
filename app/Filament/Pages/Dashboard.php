<?php

namespace App\Filament\Pages;

use App\Models\StripeCustomer;
use Filament\Actions\Action;
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
            Action::make('createInvoice')->modal()->label('Wystaw fakturę')->icon('heroicon-s-document-plus'),
            Action::make('makeAppointment')->color('gray')->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
            Action::make('sendEmail')->color('gray')->label('Wyślij email')->icon('heroicon-o-envelope')->tooltip('wkrótce'),
            Action::make('sendSMS')->color('gray')->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text')->tooltip('wkrótce'),
        ];
    }
}
