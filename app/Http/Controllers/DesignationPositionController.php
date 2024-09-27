<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Position;
use Illuminate\Http\JsonResponse;

class DesignationPositionController extends Controller
{
    /**
     * Fetch all designations and positions.
     *
     * @return JsonResponse
     */
    public function fetchDesignationsAndPositions(): JsonResponse
    {
        $designations = Designation::all();
        $positions = Position::all();

        return response()->json([
            'message' => 'Designations and Positions retrieved successfully',
            'designations' => $designations,
            'positions' => $positions,
        ]);
    }
}
