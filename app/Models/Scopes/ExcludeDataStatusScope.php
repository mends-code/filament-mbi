<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExcludeDataStatusScope implements Scope
{
    protected $statuses;

    public function __construct(array $statuses)
    {
        $this->statuses = $statuses;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNotIn('data->status', $this->statuses);
    }
}
