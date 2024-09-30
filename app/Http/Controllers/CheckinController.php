<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function checkIn(Request $request)
{
    // Validate the request
    $request->validate([
        'user_id' => 'required|string|exists:users,user_id',  // Validate user_id exists in users table
        'check_in_info' => 'nullable|string',  // Optional check-in info
    ]);

    // Retrieve the user
    $user = User::where('user_id', $request->user_id)->first();

    // Ensure the user is active and not trashed
    if ($user && $user->trash == 0) {
        // Check if there's already a check-in for today without a check-out
        $existingCheckIn = CheckIn::where('employee_id', $user->user_id)
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', Carbon::now('Asia/Kolkata')->toDateString())
            ->first();

        if ($existingCheckIn) {
            return response()->json(['message' => 'Please check out first.'], 403);
        }

        $nowIndia = Carbon::now('Asia/Kolkata');
        $status = $nowIndia->format('H:i') > '10:00' ? 'Late' : 'Active';

        // Store the new check-in record
        $checkIn = CheckIn::create([
            'employee_id' => $user->user_id,
            'status' => $status,
            'user_name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'check_in_time' => $nowIndia,
            'check_in_info' => $request->input('check_in_info'),  // Optional description
        ]);

        // Check if attendance record already exists for today
        $existingAttendance = Attendance::where('user_id', $user->user_id)
            ->whereDate('attendance_date', $nowIndia->toDateString())
            ->first();

        // Only create a new attendance record if it doesn't exist
        if (!$existingAttendance) {
            Attendance::create([
                'user_id' => $user->user_id,
                'attendance_date' => $nowIndia->toDateString(),  // Store today's date
                'check_in_time' => $nowIndia,  // Current time
                'check_in_description' => 'Entry Successful',  // Optional description
                'status' => $status,
            ]);
        }

        return response()->json([
            'message' => 'Check-in successful',
            'check_in' => $checkIn,
        ], 200);
    }

    return response()->json(['message' => 'User is not active or is trashed'], 403);
}


    // Check-out method
    public function checkOut(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|string|exists:users,user_id',  // Validate user_id exists in users table
            'check_out_info' => 'nullable|string',  // Optional check-out info
        ]);

        // Retrieve the user
        $user = User::where('user_id', $request->user_id)->first();

        // Ensure the user is active and not trashed
        if ($user && $user->trash == 0) {
            // Find the latest check-in record for today that has no check-out time
            $checkIn = CheckIn::where('employee_id', $user->user_id)
                ->whereNull('check_out_time')
                ->whereDate('check_in_time', Carbon::now('Asia/Kolkata')->toDateString())  // Only consider today's check-ins
                ->first();

            if ($checkIn) {
                // Calculate duration between check-in and check-out
                $checkOutTime = Carbon::now('Asia/Kolkata');
                $durationInHours = $checkOutTime->diffInHours($checkIn->check_in_time);

                // Determine the status based on the duration
                $status = ($durationInHours < 8) ? 'HalfDayPresent' : 'Active';

                // Update the check-in record with the check-out time and status
                $checkIn->update([
                    'check_out_time' => $checkOutTime,
                    'check_out_info' => "Exit Successful",  // Optional description
                    'status' => $status,
                ]);

                // Find the corresponding attendance record for today
                $attendance = Attendance::where('user_id', $user->user_id)
                    ->whereDate('attendance_date', Carbon::now('Asia/Kolkata')->toDateString())  // Only consider today's attendance
                    ->first();

                // If attendance record exists, update it with the check-out time and status
                if ($attendance) {
                    $attendance->update([
                        'check_out_time' => $checkOutTime,
                        'check_out_description' => $request->input('check_out_info'),  // Optional description
                        'status' => $status,  // Update the status based on the duration
                    ]);
                }

                return response()->json([
                    'message' => 'Check-out successful',
                    'check_out' => $checkIn,
                    'attendance' => $attendance,
                ], 200);
            }

            return response()->json(['message' => 'No active check-in found for today'], 404);
        }

        return response()->json(['message' => 'User is not active or is trashed'], 403);
    }


public function getCheckIns(Request $request)
{
    // Validate the request
    $request->validate([
        'user_id' => 'nullable|string|exists:users,user_id', // Validate user_id if provided
    ]);

    // If user_id is provided, fetch the check-ins for the user
    if ($request->has('user_id')) {
        $checkIns = CheckIn::where('employee_id', $request->user_id)
            ->orderBy('check_in_time', 'desc') // Order by latest check-ins first
            ->take(20) // Limit to the last 20 records
            ->get();

        if ($checkIns->isEmpty()) {
            return response()->json([
                'message' => 'No check-ins found for this user',
                'check_ins' => [],
                'average_check_in_time' => null,
                'average_check_out_time' => null,
                'last_check_out_time' => null
            ], 404);
        }

        // Convert check_in_time and check_out_time to timestamps
        $checkInTimestamps = $checkIns->pluck('check_in_time')->map(function ($time) {
            return Carbon::parse($time)->timestamp;
        });

        $checkOutTimestamps = $checkIns->pluck('check_out_time')->filter()->map(function ($time) {
            return Carbon::parse($time)->timestamp;
        });

        // Calculate average timestamps
        $averageCheckInTimestamp = $checkInTimestamps->average();
        $averageCheckOutTimestamp = $checkOutTimestamps->average();

        // Convert average timestamps back to datetime
        $averageCheckInTime = $averageCheckInTimestamp ? Carbon::createFromTimestamp($averageCheckInTimestamp)->toDateTimeString() : null;
        $averageCheckOutTime = $averageCheckOutTimestamp ? Carbon::createFromTimestamp($averageCheckOutTimestamp)->toDateTimeString() : null;

        // Get last check-out time (if available)
        $lastCheckOutTime = $checkIns->first()->check_out_time;

        return response()->json([
            'message' => 'Check-ins retrieved successfully',
            'check_ins' => $checkIns,
            'average_check_in_time' => $averageCheckInTime,
            'average_check_out_time' => $averageCheckOutTime,
            'last_check_out_time' => $lastCheckOutTime,
        ], 200);
    }

    // If no user_id is provided, return a default message
    return response()->json([
        'message' => 'No user_id provided or no check-ins found',
        'check_ins' => [],
        'average_check_in_time' => null,
        'average_check_out_time' => null,
        'last_check_out_time' => null,
    ], 404);
}

}
