<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get all pending provider applications
     */
    public function pendingProviders()
    {
        $pendingUsers = DB::select(
            "SELECT id, name, email, created_at 
             FROM users 
             WHERE role = 'provider' AND status = 'pending' 
             ORDER BY id ASC"
        );

        return response()->json(['pending_providers' => $pendingUsers]);
    }

    /**
     * Approve a provider and send notification
     */
    public function approveProvider($id)
    {
        // Update provider status
        $updated = DB::update(
            "UPDATE users SET status = 'approved' WHERE id = ?",
            [$id]
        );

        if ($updated) {
            // Ensure user exists before sending notification
            $user = DB::selectOne("SELECT id FROM users WHERE id = ?", [$id]);

            if ($user) {
                DB::insert(
                    "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
                     VALUES (?, 'approval', ?, 0, NOW(), NOW())",
                    [$user->id, "Your provider application has been approved by admin."]
                );
            }

            return response()->json(['success' => true, 'message' => 'Provider approved and notified']);
        }

        return response()->json(['success' => false, 'message' => 'Provider not found'], 404);
    }

    /**
     * Reject a provider and send notification
     */
    public function rejectProvider($id)
    {
        // Update provider status
        $updated = DB::update(
            "UPDATE users SET status = 'rejected' WHERE id = ?",
            [$id]
        );

        if ($updated) {
            // Ensure user exists before sending notification
            $user = DB::selectOne("SELECT id FROM users WHERE id = ?", [$id]);

            if ($user) {
                DB::insert(
                    "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
                     VALUES (?, 'approval', ?, 0, NOW(), NOW())",
                    [$user->id, "Your provider application has been rejected by admin."]
                );
            }

            return response()->json(['success' => true, 'message' => 'Provider rejected and notified']);
        }

        return response()->json(['success' => false, 'message' => 'Provider not found'], 404);
    }
}
