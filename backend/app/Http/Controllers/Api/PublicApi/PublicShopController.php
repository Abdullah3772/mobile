<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Review;
use Illuminate\Http\Request;

class PublicShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::where('status', 'approved');

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->boolean('verified_only')) {
            $query->where('is_verified', true);
        }

        $sortBy = $request->get('sort_by', 'rating');
        if ($sortBy === 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        }

        $shops = $query->paginate(15);
        return response()->json($shops);
    }

    public function show($slug)
    {
        $shop = Shop::where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $shop->increment('total_views');

        $products = $shop->products()
            ->with(['primaryImage', 'brand'])
            ->where('status', 'active')
            ->paginate(20);

        $reviews = Review::with('user')
            ->where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'shop' => $shop,
            'products' => $products,
            'reviews' => $reviews,
        ]);
    }

    public function topRated()
    {
        $shops = Shop::where('status', 'approved')
            ->where('is_verified', true)
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();
        return response()->json(['top_shops' => $shops]);
    }

    public function verified()
    {
        $shops = Shop::where('status', 'approved')
            ->where('is_verified', true)
            ->orderBy('rating', 'desc')
            ->paginate(15);
        return response()->json($shops);
    }
}
