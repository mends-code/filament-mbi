<?php

namespace App\Models\Cloudflare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortenedLink extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'mbi_link_shortener.shortened_links';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    // Set the primary key type to string
    protected $keyType = 'string';

    // Allow mass assignment for these fields
    protected $fillable = [
        'base64_target_url',
        'finalized_at',
        'metadata',
    ];

    // Cast attributes to appropriate types
    protected $casts = [
        'finalized_at' => 'timestamp',
        'metadata' => 'array',
    ];

    // Boot method to hook into the model's lifecycle
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a unique ID when creating a new model instance
        static::creating(function ($model) {
            $model->id = Str::random(config('services.cloudflare.link_shortener.id_length'));
        });
    }

    /**
     * Relationship to LinkEntry
     */
    public function linkEntries()
    {
        return $this->hasMany(LinkEntry::class, 'shortened_link_id', 'id');
    }

    /**
     * Get the decoded target URL
     *
     * @return string
     */
    public function getDecodedTargetUrlAttribute()
    {
        return base64_decode($this->base64_target_url);
    }
}
