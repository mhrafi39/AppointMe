<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function addPayment(Request $request)
    {
        // Validate request
        $request->validate([
            'booking_id'     => 'required|integer',
            'payment_method' => 'required|string|max:50',
            'amount_paid'    => 'required|numeric',
        ]);

        $booking_id = $request->booking_id;
        $payment_method = $request->payment_method;
        $amount_paid = $request->amount_paid;

        try {
            // Check if booking exists
            $booking = DB::selectOne('SELECT * FROM bookings WHERE booking_id = ?', [$booking_id]);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.'
                ], 404);
            }

            // Insert payment using raw SQL
            DB::insert(
                'INSERT INTO payments (booking_id, payment_method, amount_paid, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())',
                [$booking_id, $payment_method, $amount_paid]
            );

            // Update booking payment status to paid (true/1)
            DB::update(
                'UPDATE bookings SET payment_status = 1, updated_at = NOW() WHERE booking_id = ?',
                [$booking_id]
            );

            // Get service details for notification
            $serviceInfo = DB::selectOne(
                'SELECT s.name as service_name FROM services s 
                 INNER JOIN bookings b ON s.services_id = b.services_id 
                 WHERE b.booking_id = ?',
                [$booking_id]
            );

            // Create notification for the user
            DB::insert(
                'INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW())',
                [
                    $booking->user_id,
                    'payment_confirmed',
                    'Payment confirmed for "' . ($serviceInfo->service_name ?? 'your service') . '". You can now leave a review.',
                    false
                ]
            );

            // Fetch the inserted payment
            $lastId = DB::getPdo()->lastInsertId();
            $payment = DB::select('SELECT * FROM payments WHERE payment_id = ? LIMIT 1', [$lastId]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully! You can now leave a review.',
                'payment' => $payment[0] ?? null
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
