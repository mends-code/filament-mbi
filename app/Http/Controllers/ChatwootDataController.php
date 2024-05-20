<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatwootDataController extends Controller
{
    public function store(Request $request)
    {
        // Store the received data in the session
        Session::put('chatwoot_data', $request->all());

        // Return a response
        return response()->json(['status' => 'success']);
    }
}
