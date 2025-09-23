<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $admin = JWTAuth::parseToken()->authenticate(); // get admin from token

            return response()->json([
                'success' => true,
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Token invalid or expired'
            ], 401);
        }
    }
}
