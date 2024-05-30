<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone_number
 * @property int $account_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
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
 * @property-read \App\Models\ChatwootAccount|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatwootConversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patient> $patients
 * @property-read int|null $patients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StripeCustomer> $stripeCustomers
 * @property-read int|null $stripe_customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StripeInvoice> $stripeInvoices
 * @property-read int|null $stripe_invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereAdditionalAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereContactType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereCustomAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootContact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChatwootContact extends Model
{
    use HasFactory;

    protected $table = 'mbi_chatwoot.contacts';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'identifier',
        'custom_attributes',
        'middle_name',
        'last_name',
        'location',
        'country_code',
        'blocked',
    ];

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
        return $this->belongsTo(ChatwootAccount::class, 'account_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'mbi_filament.chatwoot_contacts_patients', 'chatwoot_contact_id', 'patient_id')->withTimestamps();
    }

    public function stripeCustomers()
    {
        return $this->hasMany(StripeCustomer::class, 'chatwoot_contact_id');
    }

    public function stripeInvoices()
    {
        return $this->hasManyThrough(
            StripeInvoice::class,
            StripeCustomer::class,
            'chatwoot_contact_id', // Foreign key on StripeCustomer table
            'customer_id', // Foreign key on StripeInvoice table
            'id', // Local key on ChatwootContact table
            'id' // Local key on StripeCustomer table
        );
    }

    public function conversations()
    {
        return $this->hasMany(ChatwootConversation::class, 'contact_id');
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
