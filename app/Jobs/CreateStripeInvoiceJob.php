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

    protected $items;

    protected $chatwootContactId;

    protected $chatwootAgentId;

    protected $chatwootConversationId;

    protected $chatwootAccountId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $items, $chatwootContactId, $chatwootAgentId, $chatwootConversationId, $chatwootAccountId)
    {
        $this->items = $items;
        $this->chatwootContactId = $chatwootContactId;
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
            'items' => $this->items,
            'chatwootContactId' => $this->chatwootContactId,
            'chatwootAgentId' => $this->chatwootAgentId,
            'chatwootConversationId' => $this->chatwootConversationId,
            'chatwootAccountId' => $this->chatwootAccountId,
        ]);

        try {
            $invoice = $stripeService->createInvoice(
                $this->items,
                $this->chatwootContactId,
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
