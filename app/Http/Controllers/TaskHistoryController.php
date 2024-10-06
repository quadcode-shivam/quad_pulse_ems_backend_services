<?php

namespace App\Http\Controllers;

use App\Models\TaskHistory;
use Illuminate\Http\Request;

class TaskHistoryController extends Controller
{

    public function getTaskHistory(Request $request)
    {
        try {
            // Fetch the last 5 task history entries by task_id in descending order
            $history = TaskHistory::where('task_id', $request->task_id)
                ->latest() // Orders by the created_at column in descending order
                ->take(5) // Limits the result to the last 5 entries
                ->get();
    
            // If history is found, return it
            if ($history->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task history fetched successfully.',
                    'data' => $history,
                    'code'=> 200,

                ], 200);
            } else {
                // If no history is found
                return response()->json([
                    'success' => false,
                    'message' => 'No history found for this task.',
                    'code'=> 400,
                ], 404);
            }
        } catch (\Exception $e) {
            // Handle any errors
            return response()->json([
                'success' => false,
                'message' => 'Error fetching task history.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
