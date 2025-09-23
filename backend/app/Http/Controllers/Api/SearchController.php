<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->search ?? '';

        // Pagination parameters
        $page = max((int) ($request->page ?? 1), 1);
        $perPage = (int) ($request->per_page ?? 10);
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50
        $offset = ($page - 1) * $perPage;

        // Total matching services
        $total = DB::selectOne(
            "SELECT COUNT(*) as count
             FROM services
             LEFT JOIN images ON services.user_id = images.user_id
             WHERE services.category LIKE ?",
            ["%{$searchTerm}%"]
        )->count;

        // Fetch paginated services
        $services = DB::select(
            "SELECT services.*, images.path as profile_picture
             FROM services
             LEFT JOIN images ON services.user_id = images.user_id
             WHERE services.category LIKE ?
             ORDER BY services.services_id DESC
             LIMIT $perPage OFFSET $offset",
            ["%{$searchTerm}%"]
        );

        $lastPage = ceil($total / $perPage);
        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        // Prepare pagination URLs
        $baseUrl = url('/api/search?search=' . urlencode($searchTerm));
        $prevPageUrl = $page > 1 ? $baseUrl . '&page=' . ($page - 1) : null;
        $nextPageUrl = $page < $lastPage ? $baseUrl . '&page=' . ($page + 1) : null;

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
                'prev_page_url' => $prevPageUrl,
                'next_page_url' => $nextPageUrl,
            ],
        ]);
    }
}
