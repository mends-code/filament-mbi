<?php

namespace App\Models;

/**
 * @property array $data
 * @property int $created
 * @property string $id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeProduct whereId($value)
 *
 * @mixin \Eloquent
 */
class StripeProduct extends BaseModelStripe
{
    protected $table = 'mbi_stripe.products';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    protected $fillable = [
        'id', 'data',
    ];
}
