<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicesController extends Controller
{
    // ✅ Add a new service
    public function addService(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'price' => 'required|numeric',
            'available_time' => 'required|string',
        ]);

        $now = Carbon::now();

        DB::insert(
            "INSERT INTO services (user_id, name, description, category, location, price, available_time, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $request->user_id,
                $request->name,
                $request->description,
                $request->category,
                $request->location,
                $request->price,
                $request->available_time,
                $now,
                $now
            ]
        );

        $serviceId = DB::getPdo()->lastInsertId();

        DB::insert(
            "INSERT INTO service_availabilities (services_id, is_booked, created_at, updated_at) VALUES (?, 0, ?, ?)",
            [$serviceId, $now, $now]
        );

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully!'
        ]);
    }

    // ✅ Fetch all services for homepage
    public function home_card(Request $request)
    {
        $page = max((int)($request->page ?? 1), 1);
        $perPage = min(max((int)($request->per_page ?? 12), 1), 50);
        $offset = ($page - 1) * $perPage;

        $totalRow = DB::selectOne("SELECT COUNT(*) as count FROM services");
        $total = $totalRow ? $totalRow->count : 0;

        $services = DB::select(
            "SELECT s.*, i.path as profile_picture, sa.is_booked
             FROM services s
             LEFT JOIN images i ON s.user_id = i.user_id
             LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
             ORDER BY s.services_id DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        foreach ($services as $service) {
            $service->profile_picture = $service->profile_picture ? url('storage/'.$service->profile_picture) : url('storage/default.jpeg');
            $service->is_booked = (bool)$service->is_booked;
        }

        $lastPage = ceil($total / $perPage);
        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        return response()->json([
            'success' => true,
            'services' => [
                'data' => $services,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'from' => $from,
                'to' => $to,
            ],
            'message' => 'Services fetched successfully'
        ]);
    }

    // ✅ Fetch services by category (NEW)
    public function getByCategory($category)
{
    try {
        $services = DB::select(
            "SELECT s.*, i.path as profile_picture, sa.is_booked
             FROM services s
             LEFT JOIN images i ON s.user_id = i.user_id
             LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
             WHERE s.category = ?
             ORDER BY s.services_id DESC",
            [$category]
        );

        foreach ($services as $service) {
            $service->profile_picture = $service->profile_picture 
                ? url('storage/'.$service->profile_picture) 
                : url('storage/default.jpeg');
            $service->is_booked = (bool)$service->is_booked;
        }

        return response()->json([
            'data' => $services,
            'success' => true,
            'message' => 'Category services fetched successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching services',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // ✅ Fetch single service by ID
    public function show($services_id)
    {
        $service = DB::selectOne(
            "SELECT s.*, sa.is_booked
             FROM services s
             LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
             WHERE s.services_id = ?",
            [$services_id]
        );

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $image = DB::selectOne(
            "SELECT path FROM images WHERE user_id = ? LIMIT 1",
            [$service->user_id]
        );

        $service->profile_picture = $image ? url('storage/'.$image->path) : url('storage/default.jpeg');
        $service->is_booked = (bool)$service->is_booked;

        return response()->json([
            'success' => true,
            'service' => $service
        ]);
    }

    // ✅ Delete service
    public function destroy($services_id)
    {
        $deleted = DB::delete("DELETE FROM services WHERE services_id = ?", [$services_id]);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully!'
        ]);
    }

    // ✅ Fetch logged-in user's services
    public function myServices(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $services = DB::select(
                "SELECT s.*, sa.is_booked
                 FROM services s
                 LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
                 WHERE s.user_id = ?
                 ORDER BY s.services_id DESC",
                [$user->id]
            );

            foreach ($services as $service) {
                $service->is_booked = (bool)$service->is_booked;
            }

            return response()->json([
                'success' => true,
                'data' => $services,
                'message' => 'User services fetched successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated or token invalid',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    // ✅ Fetch all services for admin
    public function adminServices()
    {
        $services = DB::select(
            "SELECT s.*, u.name as owner_name, sa.is_booked
             FROM services s
             LEFT JOIN users u ON s.user_id = u.id
             LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id
             ORDER BY s.services_id DESC"
        );

        foreach ($services as $service) {
            $service->is_booked = (bool)$service->is_booked;
        }

        return response()->json([
            'success' => true,
            'data' => $services,
            'message' => 'All services fetched successfully (admin)'
        ]);
    }
}



