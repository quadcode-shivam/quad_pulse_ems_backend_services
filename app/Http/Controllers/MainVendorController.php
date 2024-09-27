<?php

namespace App\Http\Controllers;

use App\Models\MainVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MainVendorController extends Controller
{
    public function createMainVendors(Request $request)
    {
        $rules = [
            'shop_name' => 'required|string|max:255',
            'shopkeeper_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'email' => 'required|email',
            'contact_number' => 'nullable|string|max:15',
            'alternate_number' => 'nullable|string|max:15',
            'description' => 'nullable|string',
            'service_type' => 'nullable|string',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'max_appointments_per_day' => 'nullable|integer|min:1',
            'appointment_duration_minutes' => 'nullable|integer|min:1',
            'requires_confirmation' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $return = [
                'code' => 100,
                'msg' => 'error',
                'err' => $validator->errors(),
            ];
            return response()->json($return, 422);
        }

        if (!empty($request->contact_number) && strlen($request->contact_number) < 4) {
            return response()->json(['error' => 'Contact number must be at least 4 digits long.'], 422);
        }

        $lastFourDigits = substr($request->contact_number, -4);
        $shop_id = strtoupper(substr($request->shopkeeper_name, 0, 4) . $lastFourDigits);

        $mainVendor = MainVendor::create(array_merge($request->all(), [
            'shop_id' => $shop_id,
            'status' => 3,
            'trash' => 0,
        ]));

        return response()->json(['message' => 'MainVendor created successfully', 'data' => $mainVendor], 201);
    }

    public function fetchMainVendors(Request $request)
    {
        $sort_order = $request->sort_order ?? 'asc';
        $col = $request->col ?? 'id';
        $status = $request->status;
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $pg = $page - 1;
        $start = ($pg > 0) ? $limit * $pg : 0;

        $query = MainVendor::where('trash', 0);

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->startDate && $request->endDate) {
            $query
                ->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->startDate)))
                ->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->endDate)));
        }

        $totalRows = $query->count();

        $vendors = $query
            ->orderBy($col, $sort_order)
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Main vendors retrieved successfully',
            'data' => $vendors,
            'total' => $totalRows,
            'current_page' => $page,
            'per_page' => $limit,
        ], 200);
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Find the vendor by email
        $vendor = MainVendor::where('email', $request->email)
            ->where('status', 3)  // Check if active
            ->where('trash', 0)   // Check if not trashed
            ->first();
    
        // Check if the vendor exists
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found.'], 404);
        }
    
        // Check if the password matches
        if ($request->password !== $vendor->password) {
            return response()->json(['error' => 'Invalid password.'], 401);
        }
    
        // Generate an authorization key
        $authorizationKey = base64_encode(str_shuffle('JbrFpMxLHDnbs' . rand(1111111, 9999999)));
    
        // Update the vendor's authorization key
        $updateSuccess = $vendor->update(['authorization_key' => $authorizationKey]);
    
        // Check if the update was successful
        if (!$updateSuccess) {
            return response()->json(['error' => 'Failed to update authorization key.'], 500);
        }
    
        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'authorization_key' => $authorizationKey,
                'vendor' => $vendor, // The vendor object now includes the updated authorization key
            ],
        ], 200);
    }
    

}
