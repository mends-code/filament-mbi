<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModelStripe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModelStripe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModelStripe query()
 *
 * @mixin \Eloquent
 */
class BaseModelStripe extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'data',
    ];

}
