<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Backlink;
use App\Models\Leave;
use App\Models\Salary;
use App\Models\SalaryDistribution;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Helper function to get the start and end date for different filters
    private function getDateRange($filter)
    {
        $today = Carbon::today();

        switch ($filter) {
            case 'Last 7 Days':
                return [
                    'start' => $today->subDays(6)->toDateString(),
                    'end' => $today->toDateString(),
                ];
            case 'Current Day':
                return [
                    'start' => $today->toDateString(),
                    'end' => $today->toDateString(),
                ];
            case 'Current Month':
                return [
                    'start' => $today->startOfMonth()->toDateString(),
                    'end' => $today->toDateString(),
                ];
            case 'Current Year':
                return [
                    'start' => $today->startOfYear()->toDateString(),
                    'end' => $today->toDateString(),
                ];
            case 'Last 30 Days':
                return [
                    'start' => $today->subDays(29)->toDateString(),
                    'end' => $today->toDateString(),
                ];
            default:
                return [
                    'start' => $today->toDateString(),
                    'end' => $today->toDateString(),
                ];
        }
    }

    // Method to get dashboard reports
    public function getDashboardReports(Request $request)
    {
        // Validate request input
        $request->validate([
            'date_range' => 'required',
            'user_id' => 'required',
        ]);

        // Get the date range based on the provided filter
        $dateRange = $this->getDateRange($request->date_range);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $userId = $request->user_id;

        try {
            // Fetch attendance data
            $attendance = Attendance::where('user_id', $userId)
                ->get();

                // Fetch tasks data
                $tasks = Task::
                all();
                
                // Fetch backlinks data
                $backlinks = Backlink::get();
                
                // Fetch leave data
                $leaves = Leave::where('employee_id', $userId)
                ->get();
                
                // Fetch salary data
                $salaries = SalaryDistribution::where('user_id', $userId)
                ->get();

       
            return response()->json([
                'attendanceReport' => $attendance,
                'taskReport' => $tasks,
                'backlinkReport' =>$backlinks,
                'leaveReport' => $leaves,
                'salaryReport' => $salaries, 
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }
}
