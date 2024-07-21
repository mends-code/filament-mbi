<?php

namespace App\Filament\Widgets\Chatwoot\Charts;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;

class WorkingMinutesChartWidget extends ChartWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

    protected static ?string $heading = 'Czas pracy w minutach';

    private array $intervals = [1, 5, 30];

    private function getYearFromFilter(): int
    {
        $date = Carbon::parse($this->filters['yearMonth']);

        return $date->year;
    }

    private function getMonthFromFilter(): int
    {
        $date = Carbon::parse($this->filters['yearMonth']);

        return $date->month;
    }

    private function getIntervalFromFilter(): int
    {
        return Arr::get($this->filters, 'interval');
    }

    private function getChatwootUserId(): int
    {
        return Arr::get($this->filters, 'chatwootUser');
    }

    protected function getData(): array
    {
        $data = collect($this->getMonthlyWorkingMinutes(
            $this->getYearFromFilter(),
            $this->getMonthFromFilter(),
            $this->intervals,
            $this->getChatwootUserId()
        ));

        return [
            'datasets' => [
                [
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys()->map(function ($key) {
                $number = filter_var($key, FILTER_SANITIZE_NUMBER_INT);

                return str_replace($number, CarbonInterval::minutes($number)->cascade()->forHumans(), $key);
            }),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
