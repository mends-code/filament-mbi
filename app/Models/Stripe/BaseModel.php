<?php

namespace App\Models\Stripe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'data',
    ];
}
