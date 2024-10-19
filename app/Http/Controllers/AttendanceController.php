<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function createAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
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
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'attendance_date';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = Attendance::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query
                ->whereDate('attendance_date', '>=', Carbon::parse($request->startDate))
                ->whereDate('attendance_date', '<=', Carbon::parse($request->endDate)->addDay());
        }

        $attendance = $query
            ->join('users', 'attendances.user_id', '=', 'users.user_id')  
            ->select('attendances.*', 'users.name as user_name', 'users.email as user_email') 
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        $totalRows = $query->count();

        $statuses = ['absent', 'halfday', 'fullday', 'late', 'present'];
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status] = Attendance::where('status', $status)
                ->when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
                    $query
                        ->whereDate('attendance_date', '>=', Carbon::parse($request->startDate))
                        ->whereDate('attendance_date', '<=', Carbon::parse($request->endDate)->addDay());
                })
                ->count();
        }

        return response()->json([
            'message' => 'Attendance records retrieved successfully',
            'data' => $attendance,
            'total' => $totalRows,
            'totals' => $statusCounts,
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
