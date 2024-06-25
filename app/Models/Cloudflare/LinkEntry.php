<?php

namespace App\Models\Cloudflare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $id
 * @property string|null $shortened_link_id
 * @property-read string|null $url
 * @property-read \App\Models\Cloudflare\ShortenedLink|null $shortenedLink
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry whereShortenedLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
