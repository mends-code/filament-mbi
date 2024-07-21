<?php

namespace App\Filament\Widgets\Chatwoot;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Arr;

class MonthlyMessagesStatsWidget extends BaseWidget
{
    use HandlesChatwootStatistics, InteractsWithPageFilters;

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

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Rozmowy',
                $this->getMonthlyConversationsCount(
                    $this->getYearFromFilter(),
                    $this->getMonthFromFilter(),
                    $this->getChatwootUserId()
                )
            )
                ->icon('heroicon-o-chart-bar'),
            Stat::make(
                'Wiadomości',
                $this->getMonthlyMessagesCount(
                    $this->getYearFromFilter(),
                    $this->getMonthFromFilter(),
                    $this->getChatwootUserId()
                )
            )
                ->icon('heroicon-o-chart-bar'),
            Stat::make(
                'Słowa w wiadomościach',
                $this->getMonthlyMessagesWordCount(
                    $this->getYearFromFilter(),
                    $this->getMonthFromFilter(),
                    $this->getChatwootUserId()
                )
            )
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
