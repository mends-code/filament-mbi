<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatwootDataController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->json()->all();
        session(['chatwootData' => $data]);
        Log::info('Chatwoot data stored:', $data);
        return response()->json(['status' => 'success', 'message' => 'Data stored successfully']);
    }

    public function fetch()
    {
        $data = session('chatwootData', []);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
