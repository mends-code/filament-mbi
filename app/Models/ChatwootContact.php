<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatwootContact extends BaseModelChatwoot
{
    
    protected $table = 'mbi_chatwoot.contacts';

    protected $fillable = [
        'name', 'email', 'phone_number',
        'identifier', 'custom_attributes', 'middle_name', 'last_name', 'location',
        'country_code', 'blocked'
    ];

    protected $casts = [
        'additional_attributes' => 'json',
        'custom_attributes' => 'json',
        'last_activity_at' => 'datetime',
        'blocked' => 'boolean',
    ];

    /**
     * Get the account that owns the contact.
     */
    public function account()
    {
        return $this->belongsTo(ChatwootAccount::class, 'account_id');
    }
    /**
     * The patients that belong to the contact.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'mbi_filament.chatwoot_contacts_patients', 'chatwoot_contact_id', 'patient_id');
    }

}
