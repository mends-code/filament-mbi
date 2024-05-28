<?php

// app/Jobs/CreateStripeInvoiceJob.php

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

    protected $priceId;

    protected $customerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contactId, $priceId, $customerId = null)
    {
        $this->contactId = $contactId;
        $this->priceId = $priceId;
        $this->customerId = $customerId;
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
            'priceId' => $this->priceId,
            'customerId' => $this->customerId,
        ]);

        try {
            $invoice = $stripeService->createQuickInvoice($this->contactId, $this->priceId, $this->customerId);

            Log::info('Invoice created successfully', ['invoiceId' => $invoice->id]);
        } catch (\Exception $e) {
            Log::error('Error creating invoice', ['message' => $e->getMessage()]);
        }
    }
}
