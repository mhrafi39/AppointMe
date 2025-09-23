<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProviderApplicationController extends Controller
{
    /**
     * [ADMIN] Get all pending applications.
     */
    public function index()
    {
        $applications = DB::select(
            "SELECT pa.*, u.name as user_name, u.email as user_email 
             FROM provider_applications pa
             INNER JOIN users u ON pa.user_id = u.id
             WHERE pa.status = 'pending'
             ORDER BY pa.created_at ASC"
        );

        // Transform the data to match the expected frontend structure
        $transformedApplications = array_map(function($app) {
            return [
                'id' => $app->id,
                'user_id' => $app->user_id,
                'real_name' => $app->real_name,
                'document_url' => $app->document_url,
                'status' => $app->status,
                'created_at' => $app->created_at,
                'updated_at' => $app->updated_at,
                'user' => [
                    'name' => $app->user_name,
                    'email' => $app->user_email
                ]
            ];
        }, $applications);

        return response()->json(['applications' => $transformedApplications]);
    }

    /**
     * [USER] Store a new application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'real_name' => 'required|string|max:255',
            'document_url' => 'required|url',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        // Get user data to check current status
        $userData = DB::selectOne('SELECT application_status, is_verified FROM users WHERE id = ? LIMIT 1', [$userId]);

        // Prevent duplicate pending applications
        if ($userData->application_status === 'pending' || $userData->is_verified) {
            return response()->json(['message' => 'An application is already pending or you are already verified.'], 409);
        }

        // Create the application record
        DB::insert(
            "INSERT INTO provider_applications (user_id, real_name, document_url, status, created_at, updated_at) 
             VALUES (?, ?, ?, 'pending', NOW(), NOW())",
            [$userId, $request->real_name, $request->document_url]
        );

        // Update the user's status
        DB::update(
            "UPDATE users SET application_status = 'pending', updated_at = NOW() WHERE id = ?",
            [$userId]
        );

        return response()->json(['message' => 'Application submitted successfully.'], 201);
    }

    /**
     * [ADMIN] Approve an application.
     */
    public function approve($applicationId)
    {
        // Get application with user info
        $application = DB::selectOne(
            "SELECT pa.*, u.id as user_id FROM provider_applications pa
             INNER JOIN users u ON pa.user_id = u.id
             WHERE pa.id = ? LIMIT 1",
            [$applicationId]
        );

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        // Update application status
        DB::update(
            "UPDATE provider_applications SET status = 'approved', updated_at = NOW() WHERE id = ?",
            [$applicationId]
        );

        // Update user status
        DB::update(
            "UPDATE users SET is_verified = 1, application_status = 'approved', updated_at = NOW() WHERE id = ?",
            [$application->user_id]
        );

        // Create notification for the user
        DB::insert(
            "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
             VALUES (?, 'application_approved', ?, 0, NOW(), NOW())",
            [
                $application->user_id,
                'Congratulations! Your provider application has been approved. You can now create and manage services.'
            ]
        );

        return response()->json(['message' => 'Application approved successfully and user notified.']);
    }

    /**
     * [ADMIN] Reject an application.
     */
    public function reject($applicationId)
    {
        // Get application with user info
        $application = DB::selectOne(
            "SELECT pa.*, u.id as user_id FROM provider_applications pa
             INNER JOIN users u ON pa.user_id = u.id
             WHERE pa.id = ? LIMIT 1",
            [$applicationId]
        );

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        // Update application status
        DB::update(
            "UPDATE provider_applications SET status = 'rejected', updated_at = NOW() WHERE id = ?",
            [$applicationId]
        );

        // Update user status
        DB::update(
            "UPDATE users SET application_status = 'rejected', updated_at = NOW() WHERE id = ?",
            [$application->user_id]
        );

        // Create notification for the user
        DB::insert(
            "INSERT INTO notifications (user_id, type, message, is_read, created_at, updated_at) 
             VALUES (?, 'application_rejected', ?, 0, NOW(), NOW())",
            [
                $application->user_id,
                'We regret to inform you that your provider application has been rejected. Please review our requirements and feel free to reapply.'
            ]
        );

        return response()->json(['message' => 'Application rejected successfully and user notified.']);
    }
}
