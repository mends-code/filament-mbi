<?php

namespace App\Models;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PrescriptionTemplate withoutTrashed()
 *
 * @mixin \Eloquent
 */
class PrescriptionTemplate extends BaseModel
{
    protected $table = 'mbi_filament.prescription_templates';
}
