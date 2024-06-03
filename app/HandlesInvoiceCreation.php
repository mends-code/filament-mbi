<?php

namespace App;

use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use Illuminate\Support\Facades\Log;

trait HandlesInvoiceCreation
{
    public function createInvoice(int $contactId, int $currentAgentId, array $items)
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $currentAgentId = $this->filters['chatwootCurrentAgentId'] ?? null;

        if ($contactId) {
            $customer = StripeCustomer::latestForContact($contactId)->first();

            Log::info('Dispatching CreateStripeInvoiceJob', [
                'contactId' => $contactId,
                'items' => $items,
                'customerId' => $customer->id ?? null,
                'agentId' => $currentAgentId,
            ]);

            CreateStripeInvoiceJob::dispatch($contactId, $items, $customer->id ?? null, $currentAgentId);
        } else {
            Log::warning('No contact ID found in filters.');
        }
    }
}
