<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryDistribution;
use Illuminate\Support\Facades\Validator;

class SalaryDistributionController extends Controller
{
    // Create a new salary distribution
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'salary_month' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_id' => 'required|string|unique:salary_distributions,transaction_id',
            'payment_method' => 'required|string',
            'currency' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $salaryDistribution = SalaryDistribution::create($request->all());
        return response()->json($salaryDistribution, 201);
    }

    // Update an existing salary distribution
    public function update(Request $request, $id)
    {
        $salaryDistribution = SalaryDistribution::find($id);

        if (!$salaryDistribution) {
            return response()->json(['error' => 'Salary distribution not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric',
            'status' => 'nullable|string',
            'salary_month' => 'nullable|string',
            'transaction_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'currency' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $salaryDistribution->update($request->all());
        return response()->json($salaryDistribution, 200);
    }

    // Remove a salary distribution
    public function destroy($id)
    {
        $salaryDistribution = SalaryDistribution::find($id);

        if (!$salaryDistribution) {
            return response()->json(['error' => 'Salary distribution not found'], 404);
        }

        $salaryDistribution->delete();
        return response()->json(['message' => 'Salary distribution deleted successfully'], 200);
    }

    // Fetch a single salary distribution by user_id
    public function fetchByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $salaryDistributions = SalaryDistribution::where('user_id', $request->user_id)->get();

        return response()->json($salaryDistributions, 200);
    }

    // Fetch all salary distributions
    public function fetchAll()
    {
        $salaryDistributions = SalaryDistribution::all();
        return response()->json($salaryDistributions, 200);
    }
}
