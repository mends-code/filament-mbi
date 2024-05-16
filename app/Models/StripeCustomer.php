<?php

namespace App\Models;

class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.customers';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    protected $fillable = [
        'id', 'data'
    ];

    public function contact()
    {
        return $this->belongsToMany(
            ChatwootContact::class,
            'mbi_filament.chatwoot_contact_stripe_customer',
            'stripe_customer_id',
            'chatwoot_contact_id',
            'id',
            'id'
        )->withTimestamps();
    }
}
