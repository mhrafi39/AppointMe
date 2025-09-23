<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'message' => 'required|string',
        ]);

        $inserted = DB::insert(
            'INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())',
            [$validated['name'], $validated['email'], $validated['message']]
        );

        if ($inserted) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }
}
