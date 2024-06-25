<?php

namespace App\Models\Stripe;

/**
 * 
 *
 * @property array $data
 * @property int $created
 * @property string|null $product_id
 * @property bool|null $active
 * @property string|null $currency
 * @property string|null $type
 * @property bool|null $livemode
 * @property int|null $unit_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @property-read \App\Models\Stripe\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|Price active()
 * @method static \Illuminate\Database\Eloquent\Builder|Price currency($currency)
 * @method static \Illuminate\Database\Eloquent\Builder|Price defaultForProduct($product_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Price forProduct($product_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Price liveMode()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price oneTime()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price recurring()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereLivemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUnitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Price extends BaseModel
{
    protected $table = 'mbi_stripe.prices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'currency' => 'string',
        'type' => 'string',
        'livemode' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeCurrency($query, $currency)
    {
        if (empty($currency)) {
            return $query;
        }

        return $query->where('currency', $currency);
    }

    public function scopeOneTime($query)
    {
        return $query->where('type', 'one_time');
    }

    public function scopeRecurring($query)
    {
        return $query->where('type', 'recurring');
    }

    public function scopeForProduct($query, $product_id)
    {
        return $query->where('product_id', $product_id); //in Product there is default_price collumn with price id; use this to make a new scope default for product or something similiar, to return only one default price for given product;
    }

    public function scopeDefaultForProduct($query, $product_id)
    {
        return $query->whereHas('product', function ($query) use ($product_id) {
            $query->where('id', $product_id)
                ->whereColumn('default_price', 'mbi_stripe.prices.id');
        });
    }

    public function scopeLiveMode($query)
    {
        return $query->where('livemode', true);
    }
}
