<?php

namespace App\Models;

class Patient extends BaseModel
{
    protected $table = 'mbi_filament.patients';

    protected $fillable = [
        'first_name', 'last_name', 'birthdate',
    ];

    /**
     * The chatwoot contacts that belong to the patient.
     */
    public function chatwootContacts()
    {
        return $this->belongsToMany(ChatwootContact::class, 'mbi_filament.chatwoot_contacts_patients', 'patient_id', 'chatwoot_contact_id');
    }
}
