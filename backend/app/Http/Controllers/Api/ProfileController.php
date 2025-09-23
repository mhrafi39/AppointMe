<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    // ✅ Get logged-in user info (UPDATED)
    public function getProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            // Get user data with profile picture from raw SQL
            $userData = DB::selectOne(
                'SELECT u.*, pp.url as profile_picture 
                 FROM users u 
                 LEFT JOIN profile_pictures pp ON u.id = pp.user_id 
                 WHERE u.id = ? LIMIT 1',
                [$userId]
            );

            if (!$userData) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            return response()->json(['success' => true, 'user' => $userData]);
        } catch (\Exception $e) {
            Log::error('Get Profile Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not retrieve profile.'], 500);
        }
    }

    // ✅ Update profile picture (UPDATED)
    public function updateProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|url'
            ]);

            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            // Check if profile picture record exists
            $existingRecord = DB::selectOne(
                'SELECT id FROM profile_pictures WHERE user_id = ? LIMIT 1',
                [$userId]
            );

            if ($existingRecord) {
                // Update existing record
                DB::update(
                    'UPDATE profile_pictures SET url = ?, updated_at = NOW() WHERE user_id = ?',
                    [$request->profile_picture, $userId]
                );
            } else {
                // Create new record
                DB::insert(
                    'INSERT INTO profile_pictures (user_id, url, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
                    [$userId, $request->profile_picture]
                );
            }

            // Get updated user data
            $userData = DB::selectOne(
                'SELECT u.*, pp.url as profile_picture 
                 FROM users u 
                 LEFT JOIN profile_pictures pp ON u.id = pp.user_id 
                 WHERE u.id = ? LIMIT 1',
                [$userId]
            );

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'user' => $userData
            ]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update Picture Failed: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['success' => false, 'message' => 'An internal server error occurred.'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
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
            
            // Get updated user data
            $userData = DB::selectOne(
                'SELECT u.*, pp.url as profile_picture 
                 FROM users u 
                 LEFT JOIN profile_pictures pp ON u.id = pp.user_id 
                 WHERE u.id = ? LIMIT 1',
                [$userId]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Upload profile picture
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $fileName);

                $profilePictureUrl = '/images/' . $fileName;

                // Check if profile picture record exists
                $existingRecord = DB::selectOne(
                    'SELECT id FROM profile_pictures WHERE user_id = ? LIMIT 1',
                    [$userId]
                );

                if ($existingRecord) {
                    // Update existing record
                    DB::update(
                        'UPDATE profile_pictures SET url = ?, updated_at = NOW() WHERE user_id = ?',
                        [$profilePictureUrl, $userId]
                    );
                } else {
                    // Create new record
                    DB::insert(
                        'INSERT INTO profile_pictures (user_id, url, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
                        [$userId, $profilePictureUrl]
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture uploaded successfully',
                    'url' => $profilePictureUrl
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Upload Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed'], 500);
        }
    }

    // ✅ Show profile picture
    public function show_profile_picture(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            $profilePicture = DB::selectOne(
                'SELECT url FROM profile_pictures WHERE user_id = ? LIMIT 1',
                [$userId]
            );

            $profilePictureUrl = $profilePicture ? $profilePicture->url : null;

            return response()->json([
                'success' => true,
                'profile_picture' => $profilePictureUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Show Profile Picture Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not retrieve profile picture.'], 500);
        }
    }
}




