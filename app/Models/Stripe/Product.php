<?php

namespace App\Models\Stripe;

/**
 * 
 *
 * @property array $data
 * @property int $created
 * @property string|null $name
 * @property string|null $description
 * @property string|null $default_price
 * @property bool|null $active
 * @property bool|null $livemode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stripe\Price> $prices
 * @property-read int|null $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder|Product currency($currency)
 * @method static \Illuminate\Database\Eloquent\Builder|Product liveMode()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDefaultPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLivemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends BaseModel
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
        return $this->hasMany(Price::class, 'product_id', 'id');
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
