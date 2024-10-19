<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeavePolicy;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function applyLeave(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:users,user_id',
            'leave_type' => 'required|in:sick,casual,personal,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'half_day_full_day' => 'required|in:half,full',
        ]);

        // Check if there is already a pending leave for the employee
        $existingLeave = Leave::where('employee_id', $validatedData['employee_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingLeave) {
            return response()->json([
                'error' => 'You already have a pending leave request.',
            ], 400);
        }

        // Calculate the number of leave days
        $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
        $endDate = \Carbon\Carbon::parse($validatedData['end_date']);

        // Calculate total days based on leave type
        $leaveDays = $startDate->diffInDays($endDate) + 1;  // +1 to include the start day

        // If it's a half-day leave, adjust the count
        if ($validatedData['half_day_full_day'] === 'half') {
            $leaveDays = 0.5;  // Count half a day as 0.5
        }

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

        // Retrieve the current total leaves for the employee
        $employeeLeaveRecord = Leave::where('employee_id', $validatedData['employee_id'])->first();
        // Check if the employee has a previous leave record
        if ($employeeLeaveRecord) {
            // Update the total_leaves with the new leaveDays applied
            $employeeLeaveRecord->total_leaves += $leaveDays;
            $employeeLeaveRecord->save();  // Save the updated total leaves
        } else {
            // If no leave records exist for the employee, create an initial record
            Leave::create([
                'employee_id' => $validatedData['employee_id'],
                'total_leaves' => $leaveDays,
            ]);
        }

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

            // Fetch the leave policy for the current employee
            $leavePolicy = LeavePolicy::first();  // Adjust as necessary to get the correct leave policy

            // Retrieve the last 5 leaves for the user, ordered by created_at descending
            $leaves = Leave::where('employee_id', $userId)
                ->latest()  // Order by created_at in descending order
                ->take(5)  // Limit to 5 records
                ->get();

            // Get total leave taken from the leave table for the user
            $totalLeaveTaken = Leave::where('employee_id', $userId)->value('total_leaves') ?? 0;

            // Count attendance records for the user with 'Late' and 'HalfDayPresent' statuses
            $totalHalf = Attendance::where('user_id', $userId)
                ->whereIn('status', ['Late'])
                ->count();
            $totalLate = Attendance::where('user_id', $userId)
                ->whereIn('status', ['HalfDayPresent'])
                ->count();

            // Calculate remaining leaves
            $remainingLeaves = $leavePolicy->total_leave - $totalLeaveTaken;
            $remainingHalf = $leavePolicy->total_half_day - $totalHalf;
            $remainingLate = $leavePolicy->total_late - $totalLate;

            return response()->json([
                'total_records' => $leaves->count(),
                'leaves' => $leaves,
                'total_leave' => $leavePolicy->total_leave,
                'total_leave_taken' => $totalLeaveTaken,
                'total_late' => $totalLate,
                'total_half' => $totalHalf,
                'remaining_leaves' => $remainingLeaves,
                'remaining_late' => $remainingHalf,
                'remaining_half' => $remainingLate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch leaves. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getLeaves()
    {
        try {
            $leavePolicy = LeavePolicy::first();  // Use first() to get a single record
            $leaves = Leave::select('leaves.*', 'users.name', 'users.email', 'users.mobile')
                ->leftJoin('users', 'leaves.employee_id', '=', 'users.user_id')
                ->get();

            if (!$leavePolicy) {
                return response()->json([
                    'error' => 'Leave policy not found.',
                ], 404);
            }

            $responseData = [
                'total_records' => $leaves->count(),
                'leaves' => $leaves,
                'leave_policy' => $leavePolicy,
                'total_leave' => $leavePolicy->total_leave,  // Return the total leave from the leave policy
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch leaves. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateLeaveStatus(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',  // Ensure user_id is valid
            'status' => 'required|string|in:pending,approved,suspended',  // Validate status
        ]);

        try {
            // Find the leave request for the user
            $leave = Leave::where('employee_id', $validated['user_id'])->first();  // Adjust this query based on your needs

            if (!$leave) {
                return response()->json([
                    'error' => 'Leave not found for the specified user.',
                ], 404);
            }

            // Update the status
            $leave->status = $validated['status'];
            $leave->save();

            return response()->json([
                'message' => 'Leave status updated successfully.',
                'leave' => $leave,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to update leave status. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getAllHolidays()
    {
        try {
            $holidays = Holiday::all();

            if ($holidays->isEmpty()) {
                return response()->json([
                    'error' => 'No holidays found.',
                ], 404);
            }

            $responseData = [
                'total_records' => $holidays->count(),
                'holidays' => $holidays,
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch holidays. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
