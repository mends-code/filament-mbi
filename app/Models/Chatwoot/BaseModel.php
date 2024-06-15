<?php

namespace App\Models\Chatwoot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasFactory;

    protected $fillable = [];

    public $timestamps = false;
}
