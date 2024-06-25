<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Customer;
use App\Models\Stripe\Invoice;
use Carbon\Carbon;

/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone_number
 * @property int $account_id
 * @property string $created_at
 * @property string $updated_at
 * @property array|null $additional_attributes
 * @property string|null $identifier
 * @property array|null $custom_attributes
 * @property int|null $last_activity_at
 * @property int|null $contact_type
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $location
 * @property string|null $country_code
 * @property bool $blocked
 * @property-read \App\Models\Chatwoot\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chatwoot\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Customer> $stripeCustomers
 * @property-read int|null $stripe_customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $stripeInvoices
 * @property-read int|null $stripe_invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAdditionalAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereContactType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCustomAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends BaseModel
{
    protected $table = 'mbi_chatwoot.contacts';

    protected $casts = [
        'name' => 'string',
        'additional_attributes' => 'json',
        'custom_attributes' => 'json',
        'last_activity_at' => 'timestamp',
        'blocked' => 'boolean',
        'identifier' => 'string',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function stripeCustomers()
    {
        return $this->hasMany(Customer::class, 'chatwoot_contact_id');
    }

    public function stripeInvoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            Customer::class,
            'chatwoot_contact_id', // Foreign key on Customer table
            'customer_id', // Foreign key on Invoice table
            'id', // Local key on ChatwootContact table
            'id' // Local key on Customer table
        );
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'contact_id');
    }

    public function getLastActivityAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Warsaw') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Warsaw') : null;
    }
}
