<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AdminOrUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'msg' => 'Token not provided'], 401);
            }

            // Try admin first
            try {
                $admin = JWTAuth::setToken($token)->authenticate('admin');
                if ($admin) {
                    $request->merge(['isAdmin' => true]);
                    return $next($request);
                }
            } catch (JWTException $e) {
                // Not admin, try user
                try {
                    $user = JWTAuth::setToken($token)->authenticate('api');
                    if ($user) {
                        $request->merge(['isAdmin' => false]);
                        return $next($request);
                    }
                } catch (JWTException $ex) {
                    return response()->json(['success' => false, 'msg' => 'Unauthorized'], 401);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Token invalid'], 401);
        }

        return $next($request);
    }
}
