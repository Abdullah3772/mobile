<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = auth()->user()->complaints()
            ->with(['shop', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return response()->json($complaints);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_id' => 'nullable|exists:shops,id',
            'product_id' => 'nullable|exists:products,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'screenshot' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['shop_id', 'product_id', 'subject', 'description']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('screenshot')) {
            $data['screenshot'] = $request->file('screenshot')->store('complaints', 'public');
        }

        $complaint = Complaint::create($data);

        return response()->json([
            'message' => 'Complaint submitted',
            'complaint' => $complaint,
        ], 201);
    }
}
