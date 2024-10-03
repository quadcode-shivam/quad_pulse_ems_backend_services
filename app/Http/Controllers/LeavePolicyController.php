<?php

namespace App\Http\Controllers;

use App\Models\LeavePolicy;
use Illuminate\Http\Request;

class LeavePolicyController extends Controller
{
    // Fetch all leave policies
    public function index()
    {
        $policies = LeavePolicy::all();
        return response()->json([
            'policies' => $policies,
            'total_records' => $policies->count(),
        ]);
    }

    // Update a specific leave policy
    public function update(Request $request, $id)
    {
        $request->validate([
            'total_leave' => 'required|integer',
            'total_half_day' => 'required|integer',
            'total_late' => 'required|integer',
        ]);

        $policy = LeavePolicy::findOrFail($id);
        $policy->total_leave = $request->total_leave;
        $policy->total_half_day = $request->total_half_day;
        $policy->total_late = $request->total_late;
        $policy->updated_at = now(); // Update the timestamp
        $policy->save();

        return response()->json($policy);
    }
}

