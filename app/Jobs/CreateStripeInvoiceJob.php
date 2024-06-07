<?php

namespace App\Jobs;

use App\Services\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateStripeInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contactId;

    protected $items;

    protected $customerId;

    protected $chatwootAgentId;

    protected $chatwootConversationId;

    protected $chatwootAccountId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contactId, array $items, $customerId, $chatwootAgentId, $chatwootConversationId, $chatwootAccountId)
    {
        $this->contactId = $contactId;
        $this->items = $items;
        $this->customerId = $customerId;
        $this->chatwootAgentId = $chatwootAgentId;
        $this->chatwootConversationId = $chatwootConversationId;
        $this->chatwootAccountId = $chatwootAccountId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(StripeService $stripeService)
    {
        Log::info('Creating invoice', [
            'contactId' => $this->contactId,
            'items' => $this->items,
            'customerId' => $this->customerId,
            'chatwootAgentId' => $this->chatwootAgentId,
            'chatwootConversationId' => $this->chatwootConversationId,
            'chatwootAccountId' => $this->chatwootAccountId,
        ]);

        try {
            $invoice = $stripeService->createInvoice(
                $this->contactId,
                $this->items,
                $this->customerId,
                $this->chatwootAgentId,
                $this->chatwootConversationId,
                $this->chatwootAccountId
            );

            Log::info('Invoice created successfully', ['invoiceId' => $invoice->id]);
        } catch (\Exception $e) {
            Log::error('Error creating invoice', ['message' => $e->getMessage()]);
        }
    }
}