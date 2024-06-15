<?php

namespace App\Models\Stripe;

use App\Models\Chatwoot\Contact;
use App\Models\ShortenedLink;

class Invoice extends BaseModel
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
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function chatwootContact()
    {
        return $this->hasOneThrough(
            Contact::class,
            Customer::class,
            'id', // Foreign key on Customer table
            'id', // Foreign key on ChatwootContact table
            'customer_id', // Local key on Invoice table
            'chatwoot_contact_id' // Local key on Customer table
        );
    }

    public function shortenedLinks()
    {
        return $this->hasMany(ShortenedLink::class, 'base64_target_url', 'base64_hosted_invoice_url');
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
