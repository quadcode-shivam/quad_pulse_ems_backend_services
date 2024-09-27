<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function fetchSettings(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'key_name';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = Setting::query();

        $totalRows = $query->count();

        $settings = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Settings retrieved successfully',
            'data' => $settings,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }
}
