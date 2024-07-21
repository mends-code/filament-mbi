<?php

namespace App\Filament\Widgets\Stripe\Charts;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;

class ParticipationInvoiceChartWidget extends ChartWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

    protected static ?string $heading = 'Faktury w rozmowach';

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

        $invoicesCollection = $this->getMonthlyInvoicesAsConversationParticipant($year, $month, $chatwootUserId);
        $invoicesData = $invoicesCollection->toArray();

        $labels = array_keys($invoicesData);
        $statuses = [];

        foreach ($invoicesData as $currency => $currencyData) {
            foreach ($currencyData as $status => $amount) {
                $adjustedAmount = $amount / 100;
                $statuses[$status][$currency] = $adjustedAmount;
            }
        }

        $datasets = array_map(function ($status) use ($statuses, $labels) {
            return [
                'label' => ucfirst($status),
                'data' => array_map(fn ($currency) => $statuses[$status][$currency] ?? 0, $labels),
                'stack' => 'stackedBar',
            ];
        }, array_keys($statuses));

        return [
            'datasets' => $datasets,
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
