<?php

namespace App\Models;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birthdate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatwootContact> $chatwootContacts
 * @property-read int|null $chatwoot_contacts_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient withoutTrashed()
 *
 * @mixin \Eloquent
 */
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
