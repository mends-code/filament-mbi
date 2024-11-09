<?php

namespace App\Models\Filament;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

abstract class BaseModel extends Model
{
    use HasFactory, SoftDeletes;
}
