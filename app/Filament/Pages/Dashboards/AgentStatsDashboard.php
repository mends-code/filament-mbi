<?php

namespace App\Filament\Pages\Dashboards;

use App\Filament\Widgets\Chatwoot\MonthlyMessagesStatsWidget;
use App\Filament\Widgets\Chatwoot\ResponseTimeChartWidget;
use App\Filament\Widgets\Chatwoot\WorkHoursChartWidget;
use App\Filament\Widgets\Chatwoot\WorkingTimeStatsWidget;
use App\Models\Chatwoot\Message;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

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

    /**
     * @throws Exception
     */
    private function getIntervalOptions(): array
    {
        // Array of intervals
        $intervals = [1, 5, 30];

        // Sort array in ascending order
        sort($intervals);

        // Generate intervalOptions array
        $intervalOptions = [];
        foreach ($intervals as $interval) {
            $intervalOptions[$interval] = CarbonInterval::minutes($interval)->forHumans();
        }

        return $intervalOptions;
    }

    /**
     * @throws Exception
     */
    public function filtersForm(Form $form): Form
    {

        $intervalOptions = $this->getIntervalOptions();

        $maxInterval = max(array_keys($intervalOptions));

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('yearMonth')
                            ->options(function () {
                                $months = [];

                                $earliestMessageDate = Message::senderUser(auth()->user()->chatwootUser->id)
                                    ->oldest('created_at')
                                    ->first()
                                    ->created_at;

                                $end = $earliestMessageDate->startOfMonth();
                                $start = Carbon::now()->startOfMonth();

                                while ($start->greaterThanOrEqualTo($end)) {
                                    $months[$start->format('Y-m')] = $start->isoFormat('MMMM YYYY');
                                    $start->subMonth();
                                }

                                return $months;
                            })
                            ->placeholder('Wybierz miesiąc')
                            ->label('Miesiąc i rok pracy')
                            ->native(false)
                            ->default(Carbon::now()->startOfMonth()->format('Y-m'))
                            ->reactive()
                            ->selectablePlaceholder(false),
                        Select::make('interval')
                            ->options($intervalOptions)
                            ->label('Dokładność pomiaru czasu')
                            ->placeholder('Wybierz interwał')
                            ->native(false)
                            ->reactive()
                            ->default($maxInterval)
                            ->selectablePlaceholder(false),
                    ])
                    ->columns(3),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            WorkingTimeStatsWidget::class,
            MonthlyMessagesStatsWidget::class,
            ResponseTimeChartWidget::class,
            WorkHoursChartWidget::class,
        ];
    }
}
