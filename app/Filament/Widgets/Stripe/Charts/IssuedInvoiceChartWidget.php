<?php

namespace App\Filament\Widgets\Stripe\Charts;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;

class IssuedInvoiceChartWidget extends ChartWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

    protected static ?string $heading = 'Wystawione faktury';

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
        $year = $this->getYearFromFilter();
        $month = $this->getMonthFromFilter();
        $chatwootUserId = $this->getChatwootUserId();

        $invoicesCollection = $this->getMonthlyInvoicesAsAgent($year, $month, $chatwootUserId);
        $invoicesData = $invoicesCollection->toArray();

        $labels = array_keys($invoicesData);
        $statuses = [];

        foreach ($invoicesData as $currency => $currencyData) {
            foreach ($currencyData as $status => $amount) {
                // Hardcoded division by 100 for presentation purposes
                $adjustedAmount = $amount / 100;

                $statuses[$status][$currency] = $adjustedAmount;
            }
        }

        $formattedDatasets = [];

        foreach ($statuses as $status => $statusData) {
            $dataForStatus = array_map(function ($currency) use ($statusData) {
                return $statusData[$currency] ?? 0;
            }, $labels);

            $formattedDatasets[] = [
                'label' => ucfirst($status),
                'data' => $dataForStatus,
                'stack' => 'stackedBar',
            ];
        }

        return [
            'datasets' => $formattedDatasets,
            'labels' => array_map('strtoupper', $labels),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => true,
                    ],
                    'stacked' => true,
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'stacked' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}
