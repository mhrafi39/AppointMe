<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS request
        if ($request->getMethod() === "OPTIONS") {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        // Allow multiple origins
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:3000',
            'http://127.0.0.1:5173',
            'https://test-vercel-qlttk68na-mehedi-hasan-rafis-projects.vercel.app',
            'https://testvercel-qlttk68na-mehedi-hasan-rafis-projects.vercel.app',
            'https://test-vercel-git-main-mehedi-hasan-rafis-projects.vercel.app',
            'https://test-vercel-sage-kappa.vercel.app',
        ];

        $origin = $request->headers->get('Origin');
        
        // Check if origin is in allowed list or matches vercel pattern
        if (in_array($origin, $allowedOrigins) || preg_match('/https:\/\/.*\.vercel\.app$/', $origin)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
