<?php


namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\ChatwootConversation;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Attributes\On;

class AssistantDashboard extends BaseDashboard
{
    use HasFiltersAction, HasFiltersForm;

    protected static ?string $navigationLabel = "Panel Asystenta";
    protected static ?string $title = "Panel Asystenta";
    protected static ?string $navigationIcon = "heroicon-o-hand-raised";

    public function boot()
    {
    }

    #[On('push-chatwoot-context')]
    public function pushChatwootContext($context)
    {
        $contextData = json_decode($context)->data;

        $this->filters = [
            'chatwootContactId' => $contextData->contact->id,
            'chatwootConversationDisplayId' => $contextData->conversation->id,
            'chatwootInboxId' => $contextData->conversation->inbox_id,
            'chatwootAccountId' => $contextData->conversation->account_id,
            'chatwootCurrentAgentId' => $contextData->currentAgent->id
        ];

        $this->dispatch('push-chatwoot-payload');
    }

    #[On('reset-chatwoot-context')]
    public function resetChatwootContext()
    {
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
