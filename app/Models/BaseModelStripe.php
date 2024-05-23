<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModelStripe extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public function getCreatedAttribute()
    {
        return $this->data['created'];
    }
}
