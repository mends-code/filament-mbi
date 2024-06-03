<?php

namespace App\Filament\Pages;

use App\HandlesInvoiceCreation;
use App\HasSessionFilters;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HandlesInvoiceCreation, HasSessionFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected ?string $heading = 'Panel Asystenta';

    protected ?string $subheading = 'Obsługa klienta, wystawianie faktur, umawianie wizyt';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    public function mount()
    {
        $this->js('window.addEventListener("message", event => $wire.dispatch("set-dashboard-filters", { context: event.data })); console.log("Filters set")');
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

        Log::info('Filters set', [
            'contactId' => $this->filters['chatwootContactId'],
            'conversationId' => $this->filters['chatwootConversationDisplayId'],
            'inboxId' => $this->filters['chatwootInboxId'],
            'accountId' => $this->filters['chatwootAccountId'],
            'currentAgentId' => $this->filters['chatwootCurrentAgentId'],
        ]);
    }

    protected function getHeaderActions(): array
    {
        $contactId = $this->filters['chatwootContactId'];
        $currentAgentId = $this->filters['chatwootCurrentAgentId'];
        
        return [
            Action::make('createInvoice')
                ->label('Wystaw fakturę')
                ->modalDescription('Wybierz walutę, konkretną usługę oraz jej cenę. W przypadku płatności za kilka takich samych usług możesz ustawić żądaną ilość.')
                ->icon('heroicon-s-document-plus')
                ->form($this->getInvoiceFormSchema())
                ->action(fn (array $data) => $this->createInvoice($contactId, $currentAgentId, [$data])),
            Action::make('makeAppointment')->outlined()->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
        ];
    }
}
