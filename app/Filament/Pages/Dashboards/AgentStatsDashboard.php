<?php

namespace App\Filament\Pages\Dashboards;

use App\Filament\Widgets\Chatwoot\MonthlyMessagesStatsWidget;
use App\Filament\Widgets\Chatwoot\ResponseTimeChartWidget;
use App\Filament\Widgets\Chatwoot\WorkingMinutesChartWidget;
use App\Filament\Widgets\Chatwoot\WorkingTimeStatsWidget;
use App\Filament\Widgets\Stripe\IssuedInvoiceChartWidget;
use App\Filament\Widgets\Stripe\ParticipationInvoiceChartWidget;
use App\Models\Chatwoot\Message;
use App\Models\Filament\User;
use Arr;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Livewire\Attributes\Computed;

class AgentStatsDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'agent-stats';

    protected static ?string $navigationLabel = 'Statystyki';

    protected static ?string $title = 'Statystyki';

    protected ?string $heading = 'Panel Statystyk Agenta';

    protected ?string $subheading = 'Rezultaty Twojej pracy w poszczególnych miesiącach';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 10;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('chatwootUser')
                            ->options($this->getChatwootUserOptions)
                            ->label('Agent')
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->default(auth()->user()->chatwootUser->name),
                        Select::make('yearMonth')
                            ->options($this->getYearMonthOptions)
                            ->placeholder('Wybierz miesiąc')
                            ->label('Miesiąc i rok pracy')
                            ->native(false)
                            ->default(Carbon::now()->startOfMonth()->format('Y-m'))
                            ->reactive()
                            ->selectablePlaceholder(false),
                    ])
                    ->columns(3),
            ]);
    }

    #[Computed(persist: true)]
    private function getEarliestMessageDate()
    {
        return Message::oldest('created_at')->first()->created_at;
    }

    public function mount(): void
    {
        $this->resetFilters();
    }

    public function resetFilters(): void
    {
        Arr::set($this->filters, 'chatwootUser', auth()->user()->chatwootUser->id);
        Arr::set($this->filters, 'yearMonth', Carbon::now()->startOfMonth()->format('Y-m'));
    }

    public function getWidgets(): array
    {
        return [
            WorkingTimeStatsWidget::class,
            MonthlyMessagesStatsWidget::class,
            ResponseTimeChartWidget::class,
            WorkingMinutesChartWidget::class,
            IssuedInvoiceChartWidget::class,
            ParticipationInvoiceChartWidget::class,
        ];
    }

    #[Computed(persist: true)]
    private function getYearMonthOptions(): array
    {
        $months = [];

        $earliestMessageDate = $this->getEarliestMessageDate;

        $end = $earliestMessageDate->startOfMonth();
        $start = Carbon::now()->startOfMonth();

        while ($start->greaterThanOrEqualTo($end)) {
            $months[$start->format('Y-m')] = $start->isoFormat('MMMM YYYY');
            $start->subMonth();
        }

        return $months;
    }

    #[Computed(persist: true)]
    private function getChatwootUserOptions(): array
    {
        return User::all()
            ->pluck('chatwootUser.name', 'chatwootUser.id')
            ->toArray();
    }
}
