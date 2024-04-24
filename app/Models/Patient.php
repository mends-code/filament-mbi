<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id', 'first_name', 'last_name', 'birthdate'
    ];
    /**
     * The chatwoot contacts that belong to the patient.
     */
    public function chatwootContacts()
    {
        return $this->belongsToMany(ChatwootContact::class, 'chatwoot_contacts_patients', 'patient_id', 'chatwoot_contact_id');
    }
}
