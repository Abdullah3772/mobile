<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,warning,success,danger',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $announcement = Announcement::create($request->only([
            'title', 'message', 'type', 'starts_at', 'ends_at',
        ]));

        return response()->json(['message' => 'Announcement created', 'announcement' => $announcement], 201);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'type' => 'nullable|in:info,warning,success,danger',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $announcement->update($request->only([
            'title', 'message', 'type', 'is_active', 'starts_at', 'ends_at',
        ]));

        return response()->json(['message' => 'Announcement updated', 'announcement' => $announcement]);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted']);
    }

    public function active()
    {
        $announcements = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->get();
        return response()->json(['announcements' => $announcements]);
    }
}
