<?php

namespace App\Models\Stripe;

use App\Models\Chatwoot\Contact;
use App\Models\Chatwoot\Conversation;
use App\Models\Chatwoot\User;
use App\Models\Cloudflare\ShortenedLink;
use App\Traits\HasTimestampScopes;

/**
 * 
 *
 * @property array $data
 * @property \Illuminate\Support\Carbon $created
 * @property string|null $customer_id
 * @property bool|null $livemode
 * @property string|null $currency
 * @property string|null $status
 * @property bool|null $paid
 * @property int|null $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @property string|null $base64_hosted_invoice_url
 * @property int|null $chatwoot_account_id
 * @property int|null $chatwoot_conversation_id
 * @property int|null $chatwoot_contact_id
 * @property int|null $chatwoot_agent_id
 * @property-read User|null $chatwootAgent
 * @property-read Contact|null $chatwootContact
 * @property-read Conversation|null $chatwootConversation
 * @property-read \App\Models\Stripe\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ShortenedLink> $shortenedLinks
 * @property-read int|null $shortened_links_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice active($statuses = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice discarded($statuses = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice forContact($contactId)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice forYearAndMonth(int $year, int $month)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice latestForContact($contactId)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice paid()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice unpaid()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereBase64HostedInvoiceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChatwootAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChatwootAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChatwootContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereChatwootConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereLivemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
