<?php

namespace App\Http\Controllers;

use App\Models\CompanyPolicy; // Make sure this model exists
use Illuminate\Http\Request;

class CompanyPolicyController extends Controller
{
    // Fetch all company policies
    public function index()
    {
        $policies = CompanyPolicy::all();
        return response()->json([
            'policies' => $policies,
            'total_records' => $policies->count(),
        ]);
    }

    // Create a new company policy
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $policy = CompanyPolicy::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json($policy, 201); // 201 Created status code
    }

    // Update a specific company policy
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|max:100',
        ]);

        $policy = CompanyPolicy::findOrFail($id);

        if ($request->has('title')) {
            $policy->title = $request->title;
        }
        if ($request->has('description')) {
            $policy->description = $request->description;
        }
        if ($request->has('category')) {
            $policy->category = $request->category;
        }
       

        $policy->updated_at = now(); // Update the timestamp
        $policy->save();

        return response()->json($policy);
    }
}
