<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingsController extends Controller
{
    // Fetch bookings for services created by logged-in user
    public function index(Request $request)
    {
        $user = auth('api')->user(); // logged-in user
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Ensure all user's services have availability records
        $this->ensureServiceAvailabilityRecords($user->id);

        // Nested query: get all bookings for services created by this user
        $bookings = DB::select("
            SELECT b.*, s.name as service_name, u.name as booked_by, sa.is_booked
            FROM bookings b
            INNER JOIN services s ON b.services_id = s.services_id
            INNER JOIN users u ON b.user_id = u.id
            LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
            WHERE s.user_id = ?
            ORDER BY b.booking_id DESC
        ", [$user->id]);

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    // Helper method to ensure availability records exist
    private function ensureServiceAvailabilityRecords($userId)
    {
        // Get all services for this user that don't have availability records
        $servicesWithoutAvailability = DB::select("
            SELECT s.services_id 
            FROM services s 
            LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id 
            WHERE s.user_id = ? AND sa.services_id IS NULL
        ", [$userId]);

        // Create availability records for services that don't have them
        foreach ($servicesWithoutAvailability as $service) {
            DB::table('service_availabilities')->insert([
                'services_id' => $service->services_id,
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function cancel($id)
{
    // Get booking details before deleting
    $booking = DB::table('bookings')
        ->join('services', 'bookings.services_id', '=', 'services.services_id')
        ->where('booking_id', $id)
        ->select('bookings.*', 'services.name as service_name', 'services.user_id as provider_id')
        ->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    // Delete the booking record
    $deleted = DB::delete('DELETE FROM bookings WHERE booking_id = ?', [$id]);

    if ($deleted) {
        // Check if this was the last booking for this provider
        // If so, make all their services available again
        $remainingBookings = DB::table('bookings')
            ->join('services', 'bookings.services_id', '=', 'services.services_id')
            ->where('services.user_id', $booking->provider_id)
            ->count();

        if ($remainingBookings === 0) {
            // No more bookings, make all services available
            DB::update(
                "UPDATE service_availabilities 
                 SET is_booked = 0, updated_at = NOW() 
                 WHERE services_id IN (
                     SELECT services_id FROM services WHERE user_id = ?
                 )",
                [$booking->provider_id]
            );
        }

        // Create notification for the user who made the booking
        DB::insert(
            "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
             VALUES (?, 'booking_cancelled', ?, 0, NOW(), NOW())",
            [$booking->user_id, 'Your booking for "' . $booking->service_name . '" has been cancelled by the provider.']
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully and user notified'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }
}



    public function confirm($id)
{
    // Get the booking and service details
    $booking = DB::table('bookings')
        ->join('services', 'bookings.services_id', '=', 'services.services_id')
        ->where('booking_id', $id)
        ->select('bookings.*', 'services.user_id as provider_id', 'services.name as service_name')
        ->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    // Ensure all provider's services have availability records
    $this->ensureServiceAvailabilityRecords($booking->provider_id);

    // Update booking status to confirmed
    $updated = DB::update(
        'UPDATE bookings SET status = 1, updated_at = NOW() WHERE booking_id = ?',
        [$id]
    );

    if ($updated) {
        // Mark ALL services by this provider as unavailable
        $servicesUpdated = DB::update(
            "UPDATE service_availabilities 
             SET is_booked = 1, updated_at = NOW() 
             WHERE services_id IN (
                 SELECT services_id FROM services WHERE user_id = ?
             )",
            [$booking->provider_id]
        );

        // Log for debugging
        \Log::info('Booking confirmed', [
            'booking_id' => $id,
            'provider_id' => $booking->provider_id,
            'services_updated' => $servicesUpdated
        ]);

        // Create notification for the user who made the booking
        DB::insert(
            "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
             VALUES (?, 'booking_confirmed', ?, 0, NOW(), NOW())",
            [$booking->user_id, 'Your booking for "' . $booking->service_name . '" has been confirmed by the provider.']
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed and all services marked unavailable',
            'services_updated' => $servicesUpdated,
            'debug' => [
                'booking_id' => $id,
                'provider_id' => $booking->provider_id,
                'booking_updated' => $updated
            ]
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to confirm booking'
        ], 500);
    }
}

public function available($id)
{
    // In the new business logic, individual services cannot be marked available
    // Only "Mark All Available" should make services available
    // This action is kept for compatibility but doesn't change availability
    
    $booking = DB::table('bookings')->where('booking_id', $id)->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Use "Mark All Available" to make services available'
    ]);
}

    public function complete($id)
    {
        // Get the booking details
        $booking = DB::table('bookings')
            ->join('services', 'bookings.services_id', '=', 'services.services_id')
            ->where('booking_id', $id)
            ->select('bookings.*', 'services.name as service_name', 'services.user_id as provider_id')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Update booking status to completed (status = 2)
        $updated = DB::update(
            'UPDATE bookings SET status = 2, updated_at = NOW() WHERE booking_id = ?',
            [$id]
        );

        if ($updated) {
            // Create notification for the user who made the booking
            DB::insert(
                "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
                 VALUES (?, 'booking_completed', ?, 0, NOW(), NOW())",
                [$booking->user_id, 'Your booking for "' . $booking->service_name . '" has been completed.']
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking marked as completed'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete booking'
            ], 500);
        }
    }

    // New method: Mark all provider's services as available
    public function markAllAvailable(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Mark all services by this provider as available
        $updated = DB::update(
            "UPDATE service_availabilities 
             SET is_booked = 0, updated_at = NOW() 
             WHERE services_id IN (
                 SELECT services_id FROM services WHERE user_id = ?
             )",
            [$user->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'All services marked as available',
            'updated_count' => $updated
        ]);
    }


}
