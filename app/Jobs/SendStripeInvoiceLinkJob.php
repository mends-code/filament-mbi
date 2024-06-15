<?php

namespace App\Jobs;

use App\Models\Stripe\Invoice;
use App\Services\ChatwootService;
use App\Services\CloudflareService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendStripeInvoiceLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chatwootInvoiceId;

    protected $chatwootAccountId;

    protected $chatwootContactId;

    protected $chatwootConversationId;

    protected $chatwootAgentId;

    public function __construct($chatwootInvoiceId, $chatwootAccountId, $chatwootContactId, $chatwootConversationId, $chatwootAgentId)
    {
        $this->chatwootInvoiceId = $chatwootInvoiceId;
        $this->chatwootAccountId = $chatwootAccountId;
        $this->chatwootContactId = $chatwootContactId;
        $this->chatwootConversationId = $chatwootConversationId;
        $this->chatwootAgentId = $chatwootAgentId;
    }

    public function handle(ChatwootService $chatwootService, CloudflareService $cloudflareKVService)
    {
        $invoice = Invoice::find($this->chatwootInvoiceId);

        if (! $invoice) {
            Log::error('No invoice found for ID', ['invoiceId' => $this->chatwootInvoiceId]);

            return;
        }

        // Generate the shortened link using CloudflareKVService
        $shortenedLink = $cloudflareKVService->createShortenedLink(
            $invoice->data['hosted_invoice_url'],
            $this->chatwootContactId,
            $this->chatwootAgentId,
            $this->chatwootConversationId,
            $this->chatwootAccountId
        );

        if (! $shortenedLink) {
            Log::error('Failed to create shortened link for invoice', ['invoiceId' => $this->chatwootInvoiceId]);

            return;
        }

        // Construct the shortened URL using the path (ID of the link) and domain with https
        $shortenedUrl = 'https://'.config('services.shortener.domain').'/'.$shortenedLink->id;

        $messages = [
            $shortenedUrl,
        ];

        Log::info('Sending messages to Chatwoot', ['messages' => $messages]);

        $responses = $chatwootService->sendMessages($this->chatwootAccountId, $this->chatwootConversationId, $messages);

        foreach ($responses as $response) {
            if (isset($response['error'])) {
                Log::error('Error sending message to Chatwoot', ['response' => $response]);

                return;
            }
        }

        Log::info('Messages sent successfully');
    }
}
