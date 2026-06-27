<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('owner');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('district')) {
            $query->where('district', $request->district);
        }
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $shops = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($shops);
    }

    public function show(Shop $shop)
    {
        $shop->load(['owner', 'documents', 'products']);
        return response()->json(['shop' => $shop]);
    }

    public function approve(Shop $shop)
    {
        $shop->update([
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        Notification::create([
            'user_id' => $shop->user_id,
            'title' => 'Shop Approved',
            'message' => "Your shop '{$shop->name}' has been approved!",
            'type' => 'shop_status',
        ]);

        return response()->json(['message' => 'Shop approved successfully', 'shop' => $shop]);
    }

    public function reject(Request $request, Shop $shop)
    {
        $request->validate(['reason' => 'required|string']);

        $shop->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        Notification::create([
            'user_id' => $shop->user_id,
            'title' => 'Shop Rejected',
            'message' => "Your shop '{$shop->name}' has been rejected. Reason: {$request->reason}",
            'type' => 'shop_status',
        ]);

        return response()->json(['message' => 'Shop rejected', 'shop' => $shop]);
    }

    public function suspend(Shop $shop)
    {
        $shop->update(['status' => 'suspended']);

        Notification::create([
            'user_id' => $shop->user_id,
            'title' => 'Shop Suspended',
            'message' => "Your shop '{$shop->name}' has been suspended.",
            'type' => 'shop_status',
        ]);

        return response()->json(['message' => 'Shop suspended', 'shop' => $shop]);
    }

    public function verify(Shop $shop)
    {
        $shop->update(['is_verified' => true]);
        return response()->json(['message' => 'Shop verified', 'shop' => $shop]);
    }

    public function stats()
    {
        return response()->json([
            'total' => Shop::count(),
            'pending' => Shop::where('status', 'pending')->count(),
            'approved' => Shop::where('status', 'approved')->count(),
            'rejected' => Shop::where('status', 'rejected')->count(),
            'suspended' => Shop::where('status', 'suspended')->count(),
            'verified' => Shop::where('is_verified', true)->count(),
        ]);
    }
}
