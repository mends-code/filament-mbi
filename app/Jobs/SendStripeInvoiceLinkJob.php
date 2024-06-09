<?php

namespace App\Jobs;

use App\Models\StripeInvoice;
use App\Models\User;
use App\Services\ChatwootService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendStripeInvoiceLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;
    protected $accountId;
    protected $contactId;
    protected $conversationId;
    protected $user;

    public function __construct($invoiceId, $accountId, $contactId, $conversationId, User $user)
    {
        $this->invoiceId = $invoiceId;
        $this->accountId = $accountId;
        $this->contactId = $contactId;
        $this->conversationId = $conversationId;
        $this->user = $user;
    }

    public function handle(ChatwootService $chatwootService)
    {
        Log::info('Job started', [
            'invoiceId' => $this->invoiceId,
            'accountId' => $this->accountId,
            'contactId' => $this->contactId,
            'conversationId' => $this->conversationId,
        ]);

        $invoice = StripeInvoice::find($this->invoiceId);

        if (! $invoice) {
            Log::error('No invoice found for ID', ['invoiceId' => $this->invoiceId]);
            return;
        }

        $messages = [
            $invoice->data['hosted_invoice_url'],
        ];

        Log::info('Sending messages to Chatwoot', ['messages' => $messages]);

        $responses = $chatwootService->sendMessages($this->accountId, $this->conversationId, $messages);

        foreach ($responses as $response) {
            if (isset($response['error'])) {
                Log::error('Error sending message to Chatwoot', ['response' => $response]);
                return;
            }
        }

        Notification::make()
            ->title('Saved successfully')
            ->broadcast($this->user);

        Log::info('Messages sent successfully');
    }
}
