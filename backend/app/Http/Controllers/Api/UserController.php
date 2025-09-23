<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Update user profile information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get authenticated user ID from JWT
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            
            // Build dynamic update query
            $updateFields = [];
            $updateValues = [];
            
            if ($request->has('name')) {
                $updateFields[] = 'name = ?';
                $updateValues[] = $request->name;
            }
            
            if ($request->has('phone')) {
                $updateFields[] = 'phone = ?';
                $updateValues[] = $request->phone;
            }
            
            if ($request->has('address')) {
                $updateFields[] = 'location = ?';
                $updateValues[] = $request->address;
            }
            
            if ($request->has('details')) {
                $updateFields[] = 'bio = ?';
                $updateValues[] = $request->details;
            }
            
            if (!empty($updateFields)) {
                $updateFields[] = 'updated_at = NOW()';
                $updateValues[] = $userId;
                
                $sql = 'UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
                DB::update($sql, $updateValues);
            }
            
            // Fetch updated user data
            $updatedUser = DB::select('SELECT * FROM users WHERE id = ? LIMIT 1', [$userId])[0];
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $updatedUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update user profile picture
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get authenticated user ID from JWT
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            
            // Update profile picture with raw SQL
            DB::update(
                'UPDATE users SET profile_picture = ?, updated_at = NOW() WHERE id = ?',
                [$request->profile_picture, $userId]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture' => $request->profile_picture
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile picture: ' . $e->getMessage()
            ], 500);
        }
    }
}