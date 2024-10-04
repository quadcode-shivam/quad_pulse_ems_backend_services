<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'country' => 'required|string',
            'state' => 'required|string',
            'mobile' => 'required|string|max:15|unique:users',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'country' => $request->country,
            'state' => $request->state,
            'mobile' => $request->mobile,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user || $request->password !== $user->password) {
            // Return 401 Unauthorized if credentials are invalid
            return response()->json(['message' => 'Invalid credentials or user Not found'], 401);
        }
    
        // Update the user's secret key
        $token = $this->generateToken($user);
        $user->secret_key = $token;  // Set the secret key
        $user->save();  // Save the updated user
    
        // Fetch the user with their associated employee data
        $userWithEmployee = User::where('user_id', $user->user_id)->first();
        // Check if user has an associated employee record
        if (!$userWithEmployee) {
            return response()->json(['message' => 'User does not have associated employee data'], 404);
        }
    
        // Return a successful response with user data, secret key, and employee data
        return response()->json([
            'message' => 'Login successful',
            'code' => 200,  // Success code
            'token' => $token,
            'user' => $userWithEmployee,
        ], 200);
    }
    

    // Token Generation Method
    private function generateToken($user)
    {
        $randomString = bin2hex(random_bytes(16));  
        $token = hash('sha256', $user->id . $user->email . $randomString); 
        $user->update(['api_token' => $token]);  
        return $token;
    }

    public function fetchUsers(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'id';
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $totalRows = $query->count();

        $users = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }
}
