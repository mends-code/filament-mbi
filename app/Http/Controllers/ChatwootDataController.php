<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ChatwootDataController extends Controller
{
    public function store(Request $request)
    {
        // Log the received data for debugging
        Log::info('Received Chatwoot data:', $request->all());

        // Attempt to store the received data in the session
        session(['chatwoot_data' => $request->all()]);

        // Store a test value in the session
        session(['test' => 'true']);

        // Verify session storage
        if (session()->has('chatwoot_data')) {
            Log::info('Session data successfully stored:', [session('chatwoot_data')]);
        } else {
            Log::warning('Failed to store session data.');
        }

        // Return a response
        return response()->json(['status' => 'success']);
    }
}
