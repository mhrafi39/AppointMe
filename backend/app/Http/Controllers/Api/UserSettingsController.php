<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSettingsController extends Controller
{
    // Update username
    public function updateName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        DB::update(
            'UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?',
            [$request->name, $userId]
        );

        // Get updated user data
        $updatedUser = DB::selectOne('SELECT * FROM users WHERE id = ? LIMIT 1', [$userId]);

        return response()->json([
            'success' => true,
            'msg' => 'Name updated successfully',
            'user' => $updatedUser
        ]);
    }

    // Update email
    public function updateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255|unique:users,email']);

        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        DB::update(
            'UPDATE users SET email = ?, updated_at = NOW() WHERE id = ?',
            [$request->email, $userId]
        );

        // Get updated user data
        $updatedUser = DB::selectOne('SELECT * FROM users WHERE id = ? LIMIT 1', [$userId]);

        return response()->json([
            'success' => true,
            'msg' => 'Email updated successfully',
            'user' => $updatedUser
        ]);
    }

    // Change password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        // Get current password from database
        $currentUserData = DB::selectOne('SELECT password FROM users WHERE id = ? LIMIT 1', [$userId]);

        if (!Hash::check($request->current_password, $currentUserData->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $newHashedPassword = bcrypt($request->new_password);

        DB::update(
            'UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?',
            [$newHashedPassword, $userId]
        );

        return response()->json(['message' => 'Password updated successfully']);
    }

    // Delete profile
    public function deleteProfile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        DB::delete('DELETE FROM users WHERE id = ?', [$userId]);

        return response()->json(['success' => true, 'msg' => 'User deleted successfully']);
    }
}
