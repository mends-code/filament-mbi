<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ChatwootContact extends Model
{
    use HasFactory;

    protected $table = 'mbi_chatwoot.contacts';

    protected $fillable = [
        'name', 'email', 'phone_number',
        'identifier', 'custom_attributes', 'middle_name', 'last_name', 'location',
        'country_code', 'blocked'
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

    public function customer()
    {
        return $this->belongsToMany(StripeCustomer::class, 'mbi_filament.chatwoot_contact_stripe_customer', 'chatwoot_contact_id', 'stripe_customer_id')->withTimestamps();
    }

    public function getLastActivityAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Warsaw') : null;
    }
}
