<?php

namespace App\Models;

class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.customers';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'chatwoot_contact_id' => 'integer',
    ];

    protected $fillable = [
        'id',
        'data',
        'chatwoot_contact_id',
    ];

    public function chatwootContact()
    {
        return $this->belongsTo(ChatwootContact::class, 'chatwoot_contact_id');
    }

    public function invoices()
    {
        return $this
            ->hasMany(StripeInvoice::class, 'customer_id', 'id');

    }

    public function scopeLatestForContact($query, $chatwootContactId)
    {
        return $query
            ->where('chatwoot_contact_id', $chatwootContactId)
            ->orderBy('created', 'desc');
    }
}
