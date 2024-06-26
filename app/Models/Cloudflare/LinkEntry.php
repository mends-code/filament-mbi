<?php

namespace App\Models\Cloudflare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkEntry extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'mbi_link_shortener.link_entries';

    // Allow mass assignment for these fields
    protected $fillable = [
        'data',
        'shortened_link_id',
    ];

    // Cast attributes to appropriate types
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Relationship to ShortenedLink
     */
    public function shortenedLink()
    {
        return $this->belongsTo(ShortenedLink::class, 'shortened_link_id', 'id');
    }

    /**
     * Get the URL from the data
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        return data_get($this->data, 'event.request.url');
    }
}
