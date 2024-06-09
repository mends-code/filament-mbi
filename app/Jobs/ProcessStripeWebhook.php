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

        // Check if the event ID already exists
        $event = StripeEvent::where('id', $eventId)->first();

        if ($event) {
            // Update the existing event's data column
            $event->update(['data' => $this->payload]);
        } else {
            // Create a new event record
            StripeEvent::create([
                'data' => $this->payload,
            ]);
        }

        // Dispatch a job to update the specific Stripe object
        UpdateStripeObject::dispatch($this->payload);
    }
}
