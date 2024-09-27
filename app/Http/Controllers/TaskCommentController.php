<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function fetchTaskComments(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'created_at';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = TaskComment::with(['task', 'user']);

        $totalRows = $query->count();

        $comments = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Task comments retrieved successfully',
            'data' => $comments,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }
}
