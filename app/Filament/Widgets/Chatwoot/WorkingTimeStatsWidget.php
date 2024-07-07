<?php

namespace App\Filament\Widgets\Chatwoot;

use App\Traits\Chatwoot\HandlesChatwootStatistics;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Arr;

class WorkingTimeStatsWidget extends BaseWidget
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
        return auth()->user()->chatwootUser->id;
    }

    /**
     * @throws Exception
     */
    private function minutesToHoursForHumans(int $minutes): string
    {

        return CarbonInterval::hours(intdiv($minutes, 60))->forHumans();
    }

    /**
     * @throws Exception
     */
    private function daysForHumans(int $days): string
    {
        CarbonInterval::setCascadeFactors([
            'weeks' => [PHP_INT_MAX, 'days'],
        ]);

        return CarbonInterval::days($days)->forHumans();
    }

    /**
     * @throws Exception
     */
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Dekady miesiąca',
                $this->getMonthlyWorkingTenthOfAMonth(
                    $this->getYearFromFilter(),
                    $this->getMonthFromFilter(),
                    $this->getChatwootUserId()
                )
            )
                ->icon('heroicon-o-chart-bar'),
            Stat::make(
                'Dni pracujące',
                $this->daysForHumans(
                    $this->getMonthlyWorkingDays(
                        $this->getYearFromFilter(),
                        $this->getMonthFromFilter(),
                        $this->getChatwootUserId()
                    ),
                ),
            )
                ->icon('heroicon-o-chart-bar'),
            Stat::make(
                'Czas pracy',
                $this->minutesToHoursForHumans(
                    Arr::get($this->getMonthlyWorkingMinutes(
                        $this->getYearFromFilter(),
                        $this->getMonthFromFilter(),
                        (array) $this->getIntervalFromFilter(),
                        $this->getChatwootUserId()
                    ), $this->getIntervalFromFilter()
                    ),
                )
            )
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
