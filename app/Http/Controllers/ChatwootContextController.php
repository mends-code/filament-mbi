<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatwootContextController extends Controller
{
    public function index()
    {
        // Return all stored contexts (if needed)
    }

    public function store(Request $request)
    {
        // Handle the incoming context data from Chatwoot
        $contextData = $request->all();

        // Store the context data in session
        Session::put('chatwoot_context', $contextData);

        return response()->json(['success' => true], 201);
    }

    public function show($id)
    {
        // Return a specific context (if needed)
    }

    public function update(Request $request, $id)
    {
        // Update a specific context (if needed)
    }

    public function destroy($id)
    {
        // Delete a specific context (if needed)
    }
}
