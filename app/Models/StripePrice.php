<?php

namespace App\Models;

class StripePrice extends BaseModelStripe
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
        return $this->belongsTo(StripeProduct::class, 'product_id', 'id');
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
        return $query->where('product_id', $product_id); //in StripeProduct there is default_price collumn with price id; use this to make a new scope default for product or something similiar, to return only one default price for given product;
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
