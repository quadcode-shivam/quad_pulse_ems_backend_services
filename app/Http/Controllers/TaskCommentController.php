<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Validator;

class TaskCommentController extends Controller
{
    // Fetch all comments for a specific task
    public function fetchComments(Request $request)
    {
        // Fetch comments related to the task ID
        $comments = TaskComment::where('task_id', $request->task_id)->get();
        
        if ($comments->isEmpty()) {
            return response()->json(['message' => 'No comments found for this task.'], 404);
        }

        return response()->json($comments, 200);
    }

    // Create a new comment
    public function createComment(Request $request)
    {
        // Validation for creating a comment
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id', // Ensure task exists
            'employee_id' => 'required', // Ensure employee exists
            'comment' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create the comment
        $comment = TaskComment::create([
            'task_id' => $request->task_id,
            'employee_id' => $request->employee_id,
            'comment' => $request->comment,
        ]);

        return response()->json($comment, 201); // Return created comment with 201 status
    }

    // Update an existing comment
    public function updateComment(Request $request)
    {
        // Validation for updating the comment
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Find the comment by ID
        $comment = TaskComment::find($request->task_id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }

        // Update the comment
        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json($comment, 200);
    }

    // Delete a comment
    public function deleteComment(Request $request)
    {
        // Find the comment by ID
        $comment = TaskComment::find($request->task_id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }

        // Delete the comment
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.'], 200);
    }
}
