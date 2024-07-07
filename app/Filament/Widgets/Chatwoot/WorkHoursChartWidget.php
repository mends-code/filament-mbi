<?php

namespace App\Filament\Widgets\Chatwoot;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;

class WorkHoursChartWidget extends ChartWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

    protected static ?string $heading = 'Godziny pracy';

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
        return auth()->user()->chatwootUser->id;
    }

    protected function getData(): array
    {
        $data = collect($this->getMonthlyWorkingMinutes(
            $this->getYearFromFilter(),
            $this->getMonthFromFilter(),
            $this->intervals,
            $this->getChatwootUserId()
        ));
        $dataInHours = $data->map(function ($item) {
            return (int) floor($item / 60);
        });

        return [
            'datasets' => [
                [
                    'data' => $dataInHours->values(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $dataInHours->keys()->map(function ($key) {
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
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
