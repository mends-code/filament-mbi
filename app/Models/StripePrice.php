<?php

namespace App\Models;

class StripePrice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.prices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'active_since' => 'timestamp'
    ];

    protected $fillable = [
        'id', 'data', 'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(StripeProduct::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('active_since');
    }
}
