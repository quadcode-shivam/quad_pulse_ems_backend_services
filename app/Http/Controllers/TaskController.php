<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskHistory; // Import the TaskHistory model
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Fetch tasks based on employee_id or fetch all tasks if not provided
    public function fetchTasks(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'nullable|exists:users,user_id',
        ]);

        if (isset($validatedData['employee_id'])) {
            $tasks = Task::where('employee_id', $validatedData['employee_id'])
                ->with('employee')
                ->get();
        } else {
            $tasks = Task::with('employee')->get();
        }

        return response()->json($tasks);
    }

    // Create a new task and log the action in TaskHistory
    public function createTask(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:users,user_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|integer|between:1,3', // Assuming priority is between 1 and 3
            'due_date' => 'required|date',
        ]);

        $task = Task::create($validatedData);

        // Log task creation in history
        TaskHistory::create([ // Create a new entry in the task_history table
            'task_id' => $task->id,
            'employee_id' => $validatedData['employee_id'], // Assuming employee_id is the user making the request
            'action' => 'created',
            'previous_data' => null, // No previous data when creating
            'new_data' => json_encode($task), // Store newly created task data
        ]);

        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task,
        ], 201);
    }

    // Update an existing task and log the action in TaskHistory
    public function updateTask(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'id' => 'required|exists:tasks,id', // Ensure the task ID is provided and exists
            'employee_id' => 'required|exists:users,user_id', // Ensure employee_id is provided and exists
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:todo,in progress,ready for staging,staging,ready for production,production,done,block',
            'priority' => 'sometimes|integer|between:1,3', // Assuming priority is between 1 and 3
            'due_date' => 'sometimes|date',
        ]);
    
        // Find the task by ID from the validated data
        $task = Task::findOrFail($validatedData['id']);
    
        // Store original data for history logging
        $originalData = $task->getAttributes(); // Get the current attributes of the task
    
        // Update the task with validated data
        $task->update(array_filter($validatedData)); // Filter out null values
    
        // Log the update in history
        TaskHistory::create([ // Create a new entry in the task_history table
            'task_id' => $task->id,
            'employee_id' => $validatedData['employee_id'], // Use the employee_id from the request
            'action' => 'updated', // Specify the action taken
            'previous_data' => json_encode($originalData), // Store original data
            'new_data' => json_encode($task->getAttributes()), // Store updated data
        ]);
    
        // Return a success response
        return response()->json([
            'message' => 'Task updated successfully!',
            'task' => $task,
        ]);
    }
    
    // Fetch task history for a specific task
    public function fetchTaskHistory($taskId)
    {
        $history = TaskHistory::where('task_id', $taskId)->with('employee')->get(); // Assuming you have a relationship with the User model

        return response()->json($history);
    }
}
