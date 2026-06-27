<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return response()->json(['banners' => $banners]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:5120',
            'link' => 'nullable|url',
            'position' => 'nullable|in:hero,middle,bottom,sidebar',
            'sort_order' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'link', 'position', 'sort_order', 'starts_at', 'ends_at']);
        $data['image'] = $request->file('image')->store('banners', 'public');

        $banner = Banner::create($data);
        return response()->json(['message' => 'Banner created', 'banner' => $banner], 201);
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'image' => 'nullable|image|max:5120',
            'link' => 'nullable|url',
            'position' => 'nullable|in:hero,middle,bottom,sidebar',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'link', 'position', 'sort_order', 'is_active']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        return response()->json(['message' => 'Banner updated', 'banner' => $banner]);
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return response()->json(['message' => 'Banner deleted']);
    }
}
