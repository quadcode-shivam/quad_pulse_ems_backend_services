<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetchNotifications(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'created_at';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = Notification::with('user');

        $totalRows = $query->count();

        $notifications = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Notifications retrieved successfully',
            'data' => $notifications,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }
}
