<?php

// app/Http/Controllers/StripeWebhookController.php

namespace App\Http\Controllers;

use App\Jobs\ProcessStripeWebhook;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Stripe Webhook Received:', $request->all());

        // Dispatch a job to handle the webhook
        ProcessStripeWebhook::dispatch($request->all());

        return response()->json(['status' => 'success']);
    }
}
