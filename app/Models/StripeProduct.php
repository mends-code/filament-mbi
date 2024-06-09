<?php

namespace App\Models;

/**
 * @property array $data
 * @property int $created
 * @property string $id
 * @property string|null $default_price
 * @property bool $active
 * @property bool $livemode
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
        'default_price' => 'string',
        'active' => 'boolean',
        'livemode' => 'boolean',
    ];

    public function prices()
    {
        return $this->hasMany(StripePrice::class, 'product_id', 'id');
    }

    public function scopeCurrency($query, $currency)
    {
        if (empty($currency)) {
            return $query;
        }

        return $query->whereHas('prices', function ($priceQuery) use ($currency) {
            $priceQuery->where('currency', $currency);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeLiveMode($query)
    {
        return $query->where('livemode', true);
    }
}
