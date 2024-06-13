<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
