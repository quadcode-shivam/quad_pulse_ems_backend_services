<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function applyLeave(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:users,user_id',
            'leave_type' => 'required|in:sick,vacation,personal,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'half_day_full_day' => 'required|in:half,full',
        ]);

        // Create a new leave entry
        $leave = Leave::create([
            'employee_id' => $validatedData['employee_id'],
            'leave_type' => $validatedData['leave_type'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'description' => $validatedData['description'],
            'half_day_full_day' => $validatedData['half_day_full_day'],
            'status' => 'pending',  // Default status is pending
        ]);

        // Return a response, success message or redirect
        return response()->json([
            'message' => 'Leave applied successfully',
            'leave' => $leave,
        ], 201);
    }

    // public function getLeavesByUserId(Request $request)
    // {
    //     try {
    //         $userId = $request->user_id;

    //         $leaves = Leave::where('employee_id', $userId)->get();

    //         return response()->json([
    //             'total_records' => $leaves->count(),
    //             'leaves' => $leaves,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Unable to fetch leaves. Please try again.',
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }

    public function getLeavesByUserId(Request $request)
    {
        try {
            $userId = $request->user_id;

            // Retrieve the last 5 leaves for the user, ordered by created_at descending
            $leaves = Leave::where('employee_id', $userId)
                ->latest()  // Order by created_at in descending order
                ->take(5)  // Limit to 5 records
                ->get();

            return response()->json([
                'total_records' => $leaves->count(),
                'leaves' => $leaves,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch leaves. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
