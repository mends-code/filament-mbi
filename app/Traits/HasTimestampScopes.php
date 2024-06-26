<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasTimestampScopes
{
    /**
     * Scope a query to only include records for a given year and month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @param  int  $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForYearAndMonth(Builder $query, int $year, int $month): Builder
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        return $query->whereBetween('created_at', [$start, $end]);
    }
}
