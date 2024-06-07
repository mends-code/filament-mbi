<?php

namespace App\Filament\Pages;

use App\Traits\HandlesInvoiceCreation;
use App\Traits\ManagesChatwootFilters;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HandlesInvoiceCreation, ManagesChatwootFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected ?string $heading = 'Panel Asystenta';

    protected ?string $subheading = 'Obsługa klienta, wystawianie faktur, umawianie wizyt';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    public function mount()
    {
        $this->addChatwootFiltersListener();
    }

    #[On('set-dashboard-filters')]
    public function setDashboardFilters($context)
    {
        $this->setChatwootFilters($context);
    }

    protected function getHeaderActions(): array
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $currentAgentId = $this->filters['chatwootCurrentAgentId'] ?? null;
        $chatwootConversationId = $this->filters['chatwootConversationId'] ?? null;
        $chatwootAccountId = $this->filters['chatwootAccountId'] ?? null;

        return [
            Action::make('createInvoice')
                ->label('Wystaw fakturę')
                ->modalDescription('Wybierz walutę, konkretną usługę oraz jej cenę. W przypadku płatności za kilka takich samych usług możesz ustawić żądaną ilość.')
                ->icon('heroicon-s-document-plus')
                ->form($this->getInvoiceFormSchema())
                ->action(function (array $data) use ($contactId, $currentAgentId, $chatwootConversationId, $chatwootAccountId) {
                    $this->chatwootConversationId = $chatwootConversationId;
                    $this->chatwootAccountId = $chatwootAccountId;
                    $this->chatwootAgentId = $currentAgentId;
                    $this->createInvoice($contactId, [$data]);
                }),
            Action::make('makeAppointment')
                ->outlined()
                ->label('Umów wizytę')
                ->icon('heroicon-o-calendar')
                ->tooltip('wkrótce'),
        ];
    }
}
