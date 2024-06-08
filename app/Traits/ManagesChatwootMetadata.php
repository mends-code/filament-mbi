<?php

namespace App\Traits;

trait ManagesChatwootMetadata
{
    use HasChatwootProperties;

    public function setChatwootMetadataFromFilters(array $filters)
    {
        $this->chatwootContactId = $filters['chatwootContactId'] ?? null;
        $this->chatwootAgentId = $filters['chatwootAgentId'] ?? null;
        $this->chatwootConversationId = $filters['chatwootConversationId'] ?? null;
        $this->chatwootAccountId = $filters['chatwootAccountId'] ?? null;
    }
}
