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

    public function mount()
    {
        $this->dispatch('create-chatwoot-payload');
    }

    public function boot()
    {
    }

    #[On('update-chatwoot-context')]
    public function getChatwootContext($context)
    {
        $this->filters['chatwootContactId'] = json_decode($context)->data->contact->id;
        $this->filters['chatwootConversationDisplayId'] = json_decode($context)->data->conversation->id;
        $this->filters['chatwootInboxId'] = json_decode($context)->data->conversation->inbox_id;
        $this->filters['chatwootAccountId'] = json_decode($context)->data->conversation->account_id;
        $this->filters['chatwootCurrentAgentId'] = json_decode($context)->data->currentAgent->id;
        $this->dispatch('create-chatwoot-payload');
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
