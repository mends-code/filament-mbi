<?php

namespace App\Models\Cloudflare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property string $id
 * @property string $base64_target_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $finalized_at
 * @property array|null $metadata
 * @property string|null $chatwoot_account_id
 * @property string|null $chatwoot_agent_id
 * @property string|null $chatwoot_contact_id
 * @property string|null $chatwoot_conversation_id
 * @property-read string $decoded_target_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cloudflare\LinkEntry> $linkEntries
 * @property-read int|null $link_entries_count
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereBase64TargetUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereChatwootAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereChatwootAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereChatwootContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereChatwootConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereFinalizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortenedLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
