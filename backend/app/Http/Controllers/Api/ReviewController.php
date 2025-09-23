<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function addReview(Request $request)
    {
        // Validate input
        $request->validate([
            'services_id' => 'required|integer',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|max:500',
        ]);

        $services_id = $request->services_id;
        $rating = $request->rating;
        $comment = $request->comment;
        $user_id = auth()->id(); // Get the authenticated user ID

        try {
            // First, get the provider_id from the service
            $service = DB::select('SELECT user_id FROM services WHERE services_id = ? LIMIT 1', [$services_id]);
            
            if (empty($service)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $provider_id = $service[0]->user_id;

            // Check if user has already reviewed this service
            $existingReview = DB::select(
                'SELECT review_id FROM reviews WHERE user_id = ? AND services_id = ? LIMIT 1',
                [$user_id, $services_id]
            );

            if (!empty($existingReview)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this service'
                ], 409);
            }

            // Insert review using raw SQL with user_id and provider_id
            DB::insert(
                'INSERT INTO reviews (user_id, provider_id, services_id, rating, comment, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                [$user_id, $provider_id, $services_id, $rating, $comment]
            );

            // Fetch the inserted review
            $lastId = DB::getPdo()->lastInsertId();
            $review = DB::select('SELECT * FROM reviews WHERE review_id = ? LIMIT 1', [$lastId]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'review' => $review[0] ?? null
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Operation failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}