<?php

namespace App\Models\Stripe;

use App\Models\Chatwoot\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property array $data
 * @property int $created
 * @property int|null $chatwoot_contact_id
 * @property bool|null $livemode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @property-read Contact|null $chatwootContact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stripe\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @method static Builder|Customer latestForContact($chatwootContactId)
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereChatwootContactId($value)
 * @method static Builder|Customer whereCreated($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereData($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereLivemode($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
