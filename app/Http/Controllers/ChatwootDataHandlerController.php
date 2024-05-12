<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatwootDataHandlerController extends Controller
{
    public function handle(Request $request)
    {
        $eventData = $request->json()->all();
        Log::info('Received Chatwoot data:', $eventData);

        // Store data in session
        session(['chatwootData' => $eventData]);

        return response()->json(['status' => 'Data processed successfully']);
    }

    public function displayData()
    {
        // Retrieve data from session
        $data = session('chatwootData', []);

        // Return a view and pass the data to it
        return view('filament.chatwoot-dashboard.pages.dashboard', ['data' => $data]);
    }
}
