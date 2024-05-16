<?php

namespace App\Models;

class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.customers';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'chatwoot_contact_id' => 'integer'
    ];

    protected $fillable = [
        'id', 'data', 'chatwoot_contact_id'
    ];

    public function contact()
    {
        return $this->belongsTo(ChatwootContact::class, 'chatwoot_contact_id');
    }
}
