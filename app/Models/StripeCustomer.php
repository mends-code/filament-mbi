<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class StripeCustomer
 * 
 * @property array $data
 * @property int $created
 * @property int|null $chatwoot_contact_id
 * @property string $id
 * @property-read ChatwootContact|null $chatwootContact
 * @property-read Collection|StripeInvoice[] $invoices
 * @property-read int|null $invoices_count
 *
 * @method static Builder|StripeCustomer latestForContact($chatwootContactId)
 * @method static Builder|StripeCustomer newModelQuery()
 * @method static Builder|StripeCustomer newQuery()
 * @method static Builder|StripeCustomer query()
 * @method static Builder|StripeCustomer whereChatwootContactId($value)
 * @method static Builder|StripeCustomer whereCreated($value)
 * @method static Builder|StripeCustomer whereData($value)
 * @method static Builder|StripeCustomer whereId($value)
 *
 * @mixin \Eloquent
 */
class StripeCustomer extends Model
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

    protected static function booted()
    {
        // Add anonymous global scope to exclude records without chatwoot_contact_id
        static::addGlobalScope('hasChatwootContact', function (Builder $builder) {
            $builder->whereNotNull('chatwoot_contact_id');
        });
    }

    /**
     * Get the related Chatwoot contact.
     */
    public function chatwootContact()
    {
        return $this->belongsTo(ChatwootContact::class, 'chatwoot_contact_id');
    }

    /**
     * Get the related invoices for the Stripe customer.
     */
    public function invoices()
    {
        return $this->hasMany(StripeInvoice::class, 'customer_id', 'id');
    }

    /**
     * Scope a query to only include the latest Stripe customer for a given Chatwoot contact.
     *
     * @param Builder $query
     * @param int $chatwootContactId
     * @return Builder
     */
    public function scopeLatestForContact($query, $chatwootContactId)
    {
        return $query
            ->where('chatwoot_contact_id', $chatwootContactId)
            ->orderBy('created', 'desc');
    }
}
