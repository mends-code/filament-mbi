<?php

// app/Jobs/UpdateStripeObject.php

namespace App\Jobs;

use App\Models\Stripe\Customer;
use App\Models\Stripe\Event;
use App\Models\Stripe\Invoice;
use App\Models\Stripe\Price;
use App\Models\Stripe\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateStripeObject implements ShouldQueue
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
        Log::info('Updating Stripe Object:', $this->payload);

        // Extract the object data from the event payload
        $object = $this->payload['data']['object'];
        $objectId = $object['id'];
        $objectType = $object['object'];

        // Retrieve the latest Event for the given object_id
        $latestEvent = Event::where('object_id', $objectId)
            ->orderBy('created', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestEvent) {
            // Switch payload with data from the latest event
            $object = $latestEvent->data['data']['object'];
            Log::info("Using data from latest event for object ID: {$objectId}");
        } else {
            Log::warning("No Event found for object ID: {$objectId}");
        }

        switch ($objectType) {
            case 'customer':
                $this->updateOrCreate(Customer::class, $objectId, $object);
                break;

            case 'invoice':
                $this->updateOrCreate(Invoice::class, $objectId, $object);
                break;

            case 'product':
                $this->updateOrCreate(Product::class, $objectId, $object);
                break;

            case 'price':
                $this->updateOrCreate(Price::class, $objectId, $object);
                break;

            default:
                Log::warning('Unhandled Stripe object type: '.$objectType);
        }
    }

    /**
     * Update or create a Stripe object.
     *
     * @param  string  $model
     * @param  string  $objectId
     * @param  array  $objectData
     * @return void
     */
    protected function updateOrCreate($model, $objectId, $objectData)
    {
        $model::updateOrCreate(
            ['id' => $objectId],
            ['data' => $objectData]
        );
    }
}
