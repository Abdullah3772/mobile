<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\AdPackage;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class ShopAdvertisementController extends Controller
{
    public function packages()
    {
        $packages = AdPackage::where('is_active', true)->get();
        return response()->json(['ad_packages' => $packages]);
    }

    public function myAds()
    {
        $shop = auth()->user()->shop;
        if (!$shop || !$shop->isApproved()) {
            return response()->json(['error' => 'Shop not approved'], 403);
        }
        $ads = $shop->advertisements()->with('adPackage')->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($ads);
    }

    public function purchase(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'ad_package_id' => 'required|exists:ad_packages,id',
            'title' => 'required|string|max:255',
            'banner_image' => 'required|image|max:5120',
            'link' => 'nullable|url',
            'position' => 'nullable|in:homepage_top,homepage_middle,sidebar,search_top,category_top',
        ]);

        $package = AdPackage::findOrFail($request->ad_package_id);

        $data = $request->except('banner_image');
        $data['shop_id'] = $shop->id;
        $data['banner_image'] = $request->file('banner_image')->store('ads', 'public');
        $data['starts_at'] = now();
        $data['ends_at'] = now()->addDays($package->duration_days);

        $ad = Advertisement::create($data);

        return response()->json([
            'message' => 'Advertisement submitted for approval',
            'advertisement' => $ad->load('adPackage'),
        ], 201);
    }
}
