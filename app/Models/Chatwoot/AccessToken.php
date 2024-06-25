<?php

namespace App\Models\Chatwoot;

use Illuminate\Database\Eloquent\Builder;

/**
 * 
 *
 * @property int $id
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property string|null $token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static Builder|AccessToken forAgentBot(int $agentBotId)
 * @method static Builder|AccessToken forPlatformApp(int $platformAppId)
 * @method static Builder|AccessToken forUser(int $userId)
 * @method static Builder|AccessToken newModelQuery()
 * @method static Builder|AccessToken newQuery()
 * @method static Builder|AccessToken query()
 * @method static Builder|AccessToken whereCreatedAt($value)
 * @method static Builder|AccessToken whereId($value)
 * @method static Builder|AccessToken whereOwnerId($value)
 * @method static Builder|AccessToken whereOwnerType($value)
 * @method static Builder|AccessToken whereToken($value)
 * @method static Builder|AccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccessToken extends BaseModel
{
    protected $table = 'mbi_chatwoot.access_tokens';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'owner_id' => 'int',
        'owner_type' => 'string',
    ];

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('owner_type', 'User')->where('owner_id', $userId);
    }

    public function scopeForPlatformApp(Builder $query, int $platformAppId): Builder
    {
        return $query->where('owner_type', 'PlatformApp')->where('owner_id', $platformAppId);
    }

    public function scopeForAgentBot(Builder $query, int $agentBotId): Builder
    {
        return $query->where('owner_type', 'AgentBot')->where('owner_id', $agentBotId);
    }
}
