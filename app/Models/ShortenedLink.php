<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortenedLink extends Model
{
    use HasFactory;

    protected $table = 'mbi_link_shortener.shortened_links';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'target_url',
        'finalized_at',
        'kv_expires_at',
    ];

    protected $casts = [
        'finalized_at' => 'timestamp',
        'kv_expires_at' => 'timestamp',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate a random string for the id
            $model->id = Str::random(config('services.shortener.id_length'));
        });
    }
}
