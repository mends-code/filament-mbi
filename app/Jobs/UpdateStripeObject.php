<?php

// app/Jobs/UpdateStripeObject.php

namespace App\Jobs;

use App\Models\StripeCustomer;
use App\Models\StripeInvoice;
use App\Models\StripeProduct;
use App\Models\StripePrice;
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

        switch ($objectType) {
            case 'customer':
                $this->updateOrCreate(StripeCustomer::class, $objectId, $object);
                break;

            case 'invoice':
                $this->updateOrCreate(StripeInvoice::class, $objectId, $object);
                break;

            case 'product':
                $this->updateOrCreate(StripeProduct::class, $objectId, $object);
                break;

            case 'price':
                $this->updateOrCreate(StripePrice::class, $objectId, $object);
                break;

            default:
                Log::warning('Unhandled Stripe object type: ' . $objectType);
        }
    }

    /**
     * Update or create a Stripe object.
     *
     * @param string $model
     * @param string $objectId
     * @param array $objectData
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
