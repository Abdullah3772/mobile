<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'shop', 'product']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($complaints);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'shop', 'product']);
        return response()->json(['complaint' => $complaint]);
    }

    public function respond(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_response' => 'required|string',
            'status' => 'required|in:in_progress,resolved,closed',
        ]);

        $complaint->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Response sent', 'complaint' => $complaint]);
    }
}
