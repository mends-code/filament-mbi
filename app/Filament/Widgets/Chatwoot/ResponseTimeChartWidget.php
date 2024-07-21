<?php

namespace App\Filament\Widgets\Chatwoot;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;

class ResponseTimeChartWidget extends ChartWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

    protected static ?string $heading = 'Czasy odpowiedzi';

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

    private function getChatwootUserId(): int
    {
        return Arr::get($this->filters, 'chatwootUser');
    }

    protected function getData(): array
    {

        $data = collect($this->getMonthlyResponseTimeStats(
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
            'labels' => $data->keys(),
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
