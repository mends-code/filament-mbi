<?php

namespace App\Models\Stripe;

use App\Models\Chatwoot\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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

    /**
     * Get the related Chatwoot contact.
     */
    public function chatwootContact()
    {
        return $this->belongsTo(Contact::class, 'chatwoot_contact_id');
    }

    /**
     * Get the related invoices for the Stripe customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'id');
    }

    /**
     * Scope a query to only include the latest Stripe customer for a given Chatwoot contact.
     *
     * @param  Builder  $query
     * @param  int  $chatwootContactId
     * @return Builder
     */
    public function scopeLatestForContact($query, $chatwootContactId)
    {
        return $query
            ->where('chatwoot_contact_id', $chatwootContactId)
            ->orderBy('created', 'desc');
    }
}
