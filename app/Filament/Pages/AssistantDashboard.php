<?php


namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Session;

class AssistantDashboard extends BaseDashboard
{
    use HasFiltersAction, HasFiltersForm;

    protected static ?string $navigationLabel = "Panel Asystenta";
    protected static ?string $title = "Panel Asystenta";
    protected static ?string $navigationIcon = "heroicon-o-hand-raised";

    #[Session]
    public ?array $filters = null;

    #[On('set-chatwoot-context')]
    public function setChatwootContext($context)
    {
        $contextData = json_decode($context)->data;

        $this->filters = [
            'chatwootContactId' => $contextData->contact->id,
            'chatwootConversationDisplayId' => $contextData->conversation->id,
            'chatwootInboxId' => $contextData->conversation->inbox_id,
            'chatwootAccountId' => $contextData->conversation->account_id,
            'chatwootCurrentAgentId' => $contextData->currentAgent->id
        ];
    }
    protected function getHeaderActions(): array
    {

        return [
            Action::make('createInvoice')->modal()->label('Wystaw fakturę')->icon('heroicon-s-document-plus'),
            Action::make('makeAppointment')->color('gray')->modal()->label('Umów wizytę')->icon('heroicon-o-calendar'),
            Action::make('sendEmail')->color('gray')->modal()->label('Wyślij email')->icon('heroicon-o-envelope'),
            Action::make('sendSMS')->color('gray')->modal()->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text'),
        ];
    }

}
