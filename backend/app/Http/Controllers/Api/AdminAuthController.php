<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminAuthController extends Controller
{
    // 🔑 Admin login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;

        $admins = DB::select('SELECT * FROM admin_accounts WHERE email = ? LIMIT 1', [$email]);

        if (empty($admins)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid credentials'
            ], 401);
        }

        $adminRow = $admins[0];

        if (!Hash::check($password, $adminRow->password)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid credentials'
            ], 401);
        }

        // Convert raw SQL result to Eloquent model for JWT
        $admin = Admin::find($adminRow->id);

        try {
            $token = JWTAuth::fromUser($admin);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Could not create token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'admin' => $admin,
            'msg' => 'Admin login successful',
            'redirect' => '/homepage'
        ]);
    }

    // 🔑 Admin logout
    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'success' => true,
                'msg' => 'Admin logged out successfully'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to logout'
            ], 400);
        }
    }

    // 🔑 Get current admin info
    public function me(Request $request)
    {
        try {
            $admin = JWTAuth::parseToken()->authenticate();
            return response()->json($admin);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Token invalid or expired'
            ], 401);
        }
    }

    // 🔑 Refresh token
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'token' => $newToken
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Token refresh failed'
            ], 401);
        }
    }

    // 🔑 Optional: Admin signup (for creating new admins)
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_accounts,email',
            'password' => 'required|string|min:6',
        ]);

        $name = $request->name;
        $email = $request->email;
        $password = bcrypt($request->password);

        // Insert admin into MySQL
        DB::insert(
            'INSERT INTO admin_accounts (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
            [$name, $email, $password]
        );

        // Fetch inserted admin row
        $adminRow = DB::select('SELECT * FROM admin_accounts WHERE email = ? LIMIT 1', [$email])[0];

        // Convert to Eloquent model for JWT
        $admin = Admin::find($adminRow->id);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'success' => true,
            'token' => $token,
            'admin' => $admin,
            'msg' => 'Admin registration successful'
        ]);
    }
}
