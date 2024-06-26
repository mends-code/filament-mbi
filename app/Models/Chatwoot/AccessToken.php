<?php

namespace App\Models\Chatwoot;

use Illuminate\Database\Eloquent\Builder;

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
