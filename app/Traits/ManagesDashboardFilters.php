<?php

namespace App\Traits;

use App\Models\StripeCustomer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

trait ManagesDashboardFilters
{
    use HasSessionFilters, ManagesChatwootMetadata;

    public function setChatwootFilters($context)
    {
        $contextData = json_decode($context)->data;

        Arr::set($this->filters, 'chatwootContactId', $contextData->contact->id ?? null);
        Arr::set($this->filters, 'chatwootConversationId', $contextData->conversation->id ?? null);
        Arr::set($this->filters, 'chatwootInboxId', $contextData->conversation->inbox_id ?? null);
        Arr::set($this->filters, 'chatwootAccountId', $contextData->conversation->account_id ?? null);
        Arr::set($this->filters, 'chatwootAgentId', $contextData->currentAgent->id ?? null);

        $this->setChatwootMetadataFromFilters($this->filters);
        $this->setStripeCustomerId($contextData->contact->id ?? null);

        Log::info('Chatwoot filters set', [
            'contactId' => $this->filters['chatwootContactId'],
            'conversationId' => $this->filters['chatwootConversationId'],
            'inboxId' => $this->filters['chatwootInboxId'],
            'accountId' => $this->filters['chatwootAccountId'],
            'currentAgentId' => $this->filters['chatwootAgentId'],
        ]);
    }

    protected function setStripeCustomerId($chatwootContactId)
    {
        if ($chatwootContactId) {
            $stripeCustomer = $this->getLatestStripeCustomerForContact($chatwootContactId);
            Arr::set($this->filters, 'stripeCustomerId', $stripeCustomer->id ?? null);

            Log::info('Stripe customer set', [
                'stripeCustomerId' => $this->filters['stripeCustomerId'],
            ]);
        }
    }

    protected function getLatestStripeCustomerForContact($chatwootContactId)
    {
        return StripeCustomer::latestForContact($chatwootContactId)->first();
    }

    public function addChatwootFiltersListener()
    {
        $this->js('window.addEventListener("message", event => $wire.dispatch("set-dashboard-filters", { context: event.data })); console.log("Filters set")');
    }
}
