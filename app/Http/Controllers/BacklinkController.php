<?php

namespace App\Http\Controllers;

use App\Models\Backlink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BacklinkController extends Controller
{
    // Fetch a filtered and paginated list of backlinks
public function fetchFilteredBacklinks(Request $request)
{
    $perPage = $request->input('limit', 10);  // Default to 10 if limit is not provided
    $currentPage = $request->input('page', 1);  // Default to page 1

    // Initialize query for backlinks
    $query = Backlink::where('trash', 0);  // Exclude trashed backlinks

    // Filter by completed status if provided
    if ($request->has('completed')) {
        $query->where('completed', $request->input('completed'));
    }

    // Filter by date range if provided
    if ($request->has('start_date') && $request->has('end_date')) {
        $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
    }

    // Paginate the backlinks
    $backlinks = $query->paginate($perPage, ['*'], 'page', $currentPage);

    return response()->json([
        'data' => $backlinks->items(),  // Get the items on the current page
        'total' => $backlinks->total(),  // Total number of backlinks
        'current_page' => $backlinks->currentPage(),  // Current page number
        'last_page' => $backlinks->lastPage(),  // Last page number
        'per_page' => $backlinks->perPage(),  // Items per page
    ]);
}


    public function fetchList(Request $request)
    {
        $perPage = $request->input('limit', 10);  // Default to 10 if limit is not provided
        $currentPage = $request->input('page', 1);  // Default to page 1

        // Paginate the backlinks, excluding completed ones
        $backlinks = Backlink::where('trash', 0)
            ->where('completed', false)  // Exclude checked (completed) backlinks
            ->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'data' => $backlinks->items(),  // Get the items on the current page
            'total' => $backlinks->total(),  // Total number of backlinks
            'current_page' => $backlinks->currentPage(),  // Current page number
            'last_page' => $backlinks->lastPage(),  // Last page number
            'per_page' => $backlinks->perPage(),  // Items per page
        ]);
    }

    // Store a newly created backlink in storage

    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'url' => 'required',
            'website' => 'required|string|max:255',
            'anchor_text' => 'required', 
            'status' => 'nullable|integer|in:1,2,3',  // 1 = Pending, 2 = Approved, 3 = Rejected
            'comments' => 'nullable|string|max:500',
            'date' => 'nullable|date',
            'completed' => 'nullable|boolean',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new backlink
        $backlink = Backlink::create($request->all());

        return response()->json($backlink, 201);
    }

    // Update the specified backlink in storage
    public function update(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:backlinks,id',  
            'url' => 'required',
            'website' => 'required|string|max:255',
            'anchor_text' => 'required|string|max:255',
            'status' => 'nullable|integer|in:1,2,3',
            'comments' => 'nullable|string|max:500',
            'date' => 'nullable|date',
            'completed' => 'nullable|boolean',
            'checked' => 'nullable|boolean',  
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $backlink = Backlink::findOrFail($request->id); 

        $backlink->update($request->all());

        return response()->json($backlink);
    }

    public function remove($id)
    {
        $backlink = Backlink::findOrFail($id);
        $backlink->trash = 1;  // Mark as trash
        $backlink->save();  // Save the changes

        return response()->json(null, 204);
    }

    // Update the status of the specified backlink
    public function statusUpdate(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:backlinks,id',  // Ensure the ID is provided and exists
            'status' => 'required|integer|in:1,2,3',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the backlink by ID
        $backlink = Backlink::findOrFail($request->id);

        // Update the status
        $backlink->status = $request->status;
        $backlink->save();

        return response()->json($backlink);
    }

    public function updateChecked(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',  // Ensure ids is an array
            'checked' => 'required|boolean',  // Ensure checked is a boolean
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update checked status for the specified backlinks
        Backlink::whereIn('id', $request->ids)->update(['completed' => $request->checked]);

        return response()->json([
            'message' => 'Checked status updated successfully.'
        ]);
    }
}
