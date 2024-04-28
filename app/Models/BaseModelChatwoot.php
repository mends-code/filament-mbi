<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModelChatwoot extends Model
{
    use HasFactory;
    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
    }
}
