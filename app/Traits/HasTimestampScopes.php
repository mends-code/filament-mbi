<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasTimestampScopes
{
    /**
     * Scope a query to only include records for a given year and month.
     */
    public function scopeCreatedAtYearAndMonth(Builder $query, int $year, int $month): Builder
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to include records created in the specified year and month.
     */
    public function scopeCreatedYearAndMonth(Builder $query, int $year, int $month): Builder
    {
        // Getting the start and end of the month as Unix timestamps
        $start = Carbon::create($year, $month, 1)->startOfMonth()->timestamp;
        $end = Carbon::create($year, $month, 1)->endOfMonth()->timestamp;

        return $query->whereBetween('created', [$start, $end]);
    }
}
