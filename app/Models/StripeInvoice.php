<?php

namespace App\Models;

class StripeInvoice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.invoices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'timestamp',
        'currency' => 'string',
        'status' => 'string',
        'paid' => 'boolean',
        'total' => 'integer',
        'livemode' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(StripeCustomer::class, 'customer_id', 'id');
    }

    public function chatwootContact()
    {
        return $this->hasOneThrough(
            ChatwootContact::class,
            StripeCustomer::class,
            'id', // Foreign key on StripeCustomer table
            'id', // Foreign key on ChatwootContact table
            'customer_id', // Local key on StripeInvoice table
            'chatwoot_contact_id' // Local key on StripeCustomer table
        );
    }

    public function scopeForContact($query, $contactId)
    {
        return $query->whereHas('chatwootContact', function ($query) use ($contactId) {
            $query->where('chatwoot_contact_id', $contactId);
        });
    }

    public function scopeLatestForContact($query, $contactId)
    {
        return $query->forContact($contactId)->orderBy('created', 'desc');
    }

    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    public function scopeActive($query, $statuses = ['draft', 'void', 'deleted'])
    {
        return $query->whereNotIn('status', $statuses);
    }

    public function scopeDiscarded($query, $statuses = ['void', 'deleted'])
    {
        return $query->whereIn('status', $statuses);
    }
}
