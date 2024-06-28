<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Chatwoot\ContactWidget;
use App\Filament\Widgets\Chatwoot\ConversationWidget;
use App\Filament\Widgets\Stripe\CustomerWidget;
use App\Filament\Widgets\Stripe\InvoicesWidget;
use App\Filament\Widgets\Stripe\LatestInvoiceWidget;
use App\Traits\Chatwoot\HandlesChatwootMetadata;
use App\Traits\ManagesDashboardFilters;
use App\Traits\Stripe\HandlesStripeInvoice;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HandlesChatwootMetadata, HandlesStripeInvoice, ManagesDashboardFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected ?string $heading = 'Panel Asystenta';

    protected ?string $subheading = 'Obsługa klienta, wystawianie faktur, umawianie wizyt';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    public function getWidgets(): array
    {
        return [
            ContactWidget::class,
            ConversationWidget::class,
            CustomerWidget::class,
            LatestInvoiceWidget::class,
            InvoicesWidget::class,
        ];
    }

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
        $this->setChatwootMetadataFromFilters($this->filters);

        return [
            Action::make('createInvoice')
                ->label('Wystaw fakturę')
                ->modalDescription('Wybierz walutę, konkretną usługę oraz jej cenę. W przypadku płatności za kilka takich samych usług możesz ustawić żądaną ilość.')
                ->icon('heroicon-s-document-plus')
                ->form($this->getInvoiceFormSchema())
                ->action(function (array $data) {
                    $this->createInvoice([$data]);
                }),
            Action::make('makeAppointment')
                ->outlined()
                ->label('Umów wizytę')
                ->icon('heroicon-o-calendar')
                ->tooltip('wkrótce'),
        ];
    }
}
