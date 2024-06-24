<?php

namespace App\Models\Stripe;

use App\Models\Chatwoot\Contact;
use App\Models\Chatwoot\Conversation;
use App\Models\Chatwoot\User;
use App\Models\ShortenedLink;
use App\Traits\HasTimestampScopes;

class Invoice extends BaseModel
{
    use HasTimestampScopes;

    protected $table = 'mbi_stripe.invoices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'datetime',
        'currency' => 'string',
        'status' => 'string',
        'paid' => 'boolean',
        'total' => 'integer',
        'livemode' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function chatwootAgent()
    {
        return $this->belongsTo(User::class, 'chatwoot_agent_id', 'id');
    }

    public function chatwootConversation()
    {
        return $this->belongsTo(Conversation::class, 'chatwoot_conversation_id', 'id');
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
