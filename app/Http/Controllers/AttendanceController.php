<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance; // Correctly import the Attendance model


class AttendanceController extends Controller
{
    
    public function createAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',  // Validate that employee exists
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
        ]);

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Attendance record created successfully',
            'data' => $attendance,
        ], 201);
    }

    

   public function fetchAttendance(Request $request)
{
    // Set default values for sorting and pagination
    $sort_order = $request->sort_order ?? 'asc';
    $col = $request->col ?? 'attendance_date'; // Sort by attendance_date by default
    $limit = $request->limit ?? 10;
    $page = $request->page ?? 1;

    $pg = $page - 1;
    $start = ($pg > 0) ? $limit * $pg : 0;

    // Query attendance data
    $query = Attendance::whereHas('employee', function ($query) {
        $query->whereHas('user', function ($query) {
            $query->where('trash', 0);  // Ensure that users who are not deleted are selected
        });
    });

    // Apply filters if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('startDate') && $request->filled('endDate')) {
        $query->whereDate('attendance_date', '>=', $request->startDate)
              ->whereDate('attendance_date', '<=', $request->endDate);
    }

    // Total rows for pagination
    $totalRows = $query->count();

    // Fetch attendance records with sorting and pagination
    $attendance = $query
        ->orderBy($col, $sort_order)
        ->offset($start)
        ->limit($limit)
        ->get([
            'id',
            'user_id',
            'attendance_date',
            'check_in_time',
            'check_in_description',
            'check_out_time',
            'check_out_description',
            'status',
            'created_at',
            'updated_at'
        ]);

    // Calculate the counts for each status
    $statuses = ['absent', 'halfday', 'fullday', 'late', 'present'];
    $statusCounts = [];

    foreach ($statuses as $status) {
        $statusCounts[$status] = Attendance::where('status', $status)
            ->whereHas('employee', function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('trash', 0);
                });
            })
            ->when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
                $query->whereDate('attendance_date', '>=', $request->startDate)
                      ->whereDate('attendance_date', '<=', $request->endDate);
            })
            ->count();
    }

    // Return the response with attendance records and status counts
    return response()->json([
        'message' => 'Attendance records retrieved successfully',
        'data' => $attendance,
        'total' => $totalRows,
        'totals' => $statusCounts, // Include status counts
        'current_page' => $page,
        'per_page' => $limit,
    ], 200);
}

    

    /**
     * Update an attendance record.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionAttendance(Request $request)
    {
        $request->validate([
            'action' => 'required|in:present,absent,late,fullday,halfday',
        ]);

        $attendance = Attendance::findOrFail($request->id);
        $attendance->status = $request->action;
        $attendance->save();

        return response()->json([
            'message' => 'Attendance record updated successfully',
            'data' => $attendance,
        ]);
    }

    /**
     * Delete an attendance record.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance record deleted successfully',
        ]);
    }
}
