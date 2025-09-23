<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAuthController extends Controller
{
    // 🔑 Login with raw SQL + JWT
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;

        $users = DB::select('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);

        if (empty($users)) {
            return response()->json(['success' => false, 'msg' => 'Invalid credentials'], 401);
        }

        $userRow = $users[0];

        if (!Hash::check($password, $userRow->password)) {
            return response()->json(['success' => false, 'msg' => 'Invalid credentials'], 401);
        }

        // Convert raw SQL result to Eloquent model for JWT
        $user = User::find($userRow->id);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'msg' => 'Could not create token'], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'msg' => 'User login successful'
        ]);
    }

    // 🔑 Signup with raw SQL + JWT
    public function signup(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $name = $request->name;
        $email = $request->email;
        $password = bcrypt($request->password);

        // Insert user into MySQL
        DB::insert(
            'INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
            [$name, $email, $password]
        );

        // Fetch inserted user row
        $userRow = DB::select('SELECT * FROM users WHERE email = ? LIMIT 1', [$email])[0];

        // Convert to Eloquent model for JWT
        $user = User::find($userRow->id);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'msg' => 'User registration successful'
        ]);
    }

    // 🔑 Logout (invalidate JWT)
    public function logout()
    {
        auth('api')->logout(); // ✅ Specify the 'api' guard

        return response()->json(['message' => 'Successfully logged out']);
    }

    // 🔑 Get currently logged-in user
    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'msg' => 'Token invalid or expired'], 401);
        }
    }

    // 🔑 Refresh JWT token
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['success' => true, 'token' => $newToken]);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'msg' => 'Token refresh failed'], 401);
        }
    }
}
