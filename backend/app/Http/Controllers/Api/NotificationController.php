<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch all notifications for logged-in user
    public function index()
    {
        try {
            $userId = Auth::id(); // logged-in user ID

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $notifications = DB::select(
                "SELECT id, type, message, is_read, created_at 
                 FROM notifications 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC",
                [$userId]
            );

            return response()->json(['notifications' => $notifications]);
        } catch (\Exception $e) {
            \Log::error('Notification fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    // Get unread notifications count
    public function unreadCount()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $count = DB::select(
                "SELECT COUNT(*) as unread_count 
                 FROM notifications 
                 WHERE user_id = ? AND is_read = 0",
                [$userId]
            );

            return response()->json(['unread_count' => $count[0]->unread_count]);
        } catch (\Exception $e) {
            \Log::error('Notification count error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get notification count'], 500);
        }
    }

    // Mark all notifications as read
    public function markRead()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            DB::update(
                "UPDATE notifications 
                 SET is_read = 1, updated_at = NOW() 
                 WHERE user_id = ? AND is_read = 0",
                [$userId]
            );

            return response()->json(['message' => 'Notifications marked as read']);
        } catch (\Exception $e) {
            \Log::error('Notification mark read error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notifications as read'], 500);
        }
    }

    // Mark a single notification as read
    public function markSingleRead($id)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // First check if the notification belongs to the authenticated user
            $notification = DB::select(
                "SELECT id FROM notifications WHERE id = ? AND user_id = ?",
                [$id, $userId]
            );

            if (empty($notification)) {
                return response()->json(['error' => 'Notification not found or unauthorized'], 404);
            }

            // Mark the specific notification as read
            DB::update(
                "UPDATE notifications 
                 SET is_read = 1, updated_at = NOW() 
                 WHERE id = ? AND user_id = ?",
                [$id, $userId]
            );

            return response()->json(['message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            \Log::error('Single notification mark read error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notification as read'], 500);
        }
    }

    // Create a new notification (admin can specify user)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        DB::insert(
            "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
             VALUES (?, ?, ?, 0, NOW(), NOW())",
            [$request->user_id, $request->type, $request->message]
        );

        return response()->json(['message' => 'Notification created successfully']);
    }
}
