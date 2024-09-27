<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function fetchTasks(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'due_date';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = Task::with('employee');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $totalRows = $query->count();

        $tasks = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data' => $tasks,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }
}
