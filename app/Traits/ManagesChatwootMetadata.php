<?php

namespace App\Traits;

trait ManagesChatwootMetadata
{
    public $chatwootContactId;

    public $chatwootAgentId;

    public $chatwootConversationId;

    public $chatwootAccountId;

    public function setChatwootMetadataFromFilters()
    {
        $filters = $this->filters;

        $this->chatwootContactId = $filters['chatwootContactId'] ?? null;
        $this->chatwootAgentId = $filters['chatwootAgentId'] ?? null;
        $this->chatwootConversationId = $filters['chatwootConversationId'] ?? null;
        $this->chatwootAccountId = $filters['chatwootAccountId'] ?? null;
    }
}
