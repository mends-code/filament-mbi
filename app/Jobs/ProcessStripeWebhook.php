<?php

// app/Jobs/ProcessStripeWebhook.php

namespace App\Jobs;

use App\Models\StripeEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStripeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log the payload for debugging purposes
        Log::info('Processing Stripe Webhook:', $this->payload);

        // Get the event ID and payload
        $eventId = $this->payload['id'];

        // Use the createOrUpdate method to handle the event
        $this->createOrUpdateStripeEvent($eventId, $this->payload);

        // Dispatch a job to update the specific Stripe object
        UpdateStripeObject::dispatch($this->payload);
    }

    /**
     * Create or update a Stripe event.
     *
     * @param string $eventId
     * @param array $payload
     * @return void
     */
    protected function createOrUpdateStripeEvent($eventId, $payload)
    {
        $event = StripeEvent::updateOrCreate(
            ['id' => $eventId],
            ['data' => $payload]
        );
    }
}
