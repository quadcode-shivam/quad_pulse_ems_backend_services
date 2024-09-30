<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function createEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'account_type' => 'required|string|in:admin,employee',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid parameters', 'errors' => $validator->errors()], 400);
        }

        $randomNumber = rand(10000, 99999);
        $userId = 'EMP' . strtoupper(substr($request->user_name, 0, 3)) . $randomNumber;

        // Set current date and time
        $currentDateTime =  Carbon::now('Asia/Kolkata');

        // Create user
        $user = User::create([
            'user_id' => $userId,  // Ensure user_id is included
            'name' => $request->user_name,
            'email' => $request->user_email,
            'password' => "123456",  // Make sure to set a password
            'country' => $request->country,
            'state' => $request->state,
            'mobile' => $request->phone,
            'address' => $request->address,
            'role' => $request->account_type,
            'created_at' => $currentDateTime,
            'updated_at' => $currentDateTime,
            'position' => $request->position,
            'designation' => $request->designation,
            'date_hired' => $currentDateTime,
            'trash' => 0,
        ]);

    
        // Prepare response data
        $responseData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile,
                'country' => $user->country,
                'state' => $user->state,
                'address' => $user->address,
                'role' => $user->role,
                'user_id' => $user->user_id,
                'position' => $user->position,
                'department' => $user->department,
                'hire_date' => $user->hire_date,
                'status' => $user->status,
            ],
        ];

        return response()->json(['message' => 'Employee created successfully', 'data' => $responseData], 201);
    }

    public function fetchEmployees(Request $request)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'sort_order' => 'in:asc,desc',
            'col' => 'string|in:user_id,name,email,date_hired,created_at,status',  // include columns you want to sort by
            'limit' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'status' => 'in:active,inactive',
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid parameters', 'errors' => $validator->errors()], 400);
        }

        // Default values
        $sort_order = $request->get('sort_order', 'asc');
        $col = $request->get('col', 'user_id');
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        // Build the query using Eloquent
        $query = User::where('trash', 0);  // Only retrieve non-trashed users
        // Filter by user status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if both start and end dates are provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Fetch users with sorting and pagination
        $users = $query
            ->orderBy($col, $sort_order)
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users->items(),
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
        ], 200);
    }

    public function updateTrashStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $userId = $request->id;

        try {
            $affectedRows = DB::table('users')
                ->update(['trash' => 1]);

            if ($affectedRows > 0) {
                return response()->json(['message' => 'Trash status updated successfully'], 200);
            } else {
                return response()->json(['message' => 'No employee found or status already set'], 404);
            }
        } catch (\Exception $e) {
            // Consider logging the exception here
            Log::error('Error updating trash status: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error updating trash status',
                'error' => 'An internal server error occurred.'
            ], 500);
        }
    }
}
