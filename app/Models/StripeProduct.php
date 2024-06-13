<?php

namespace App\Models;

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
