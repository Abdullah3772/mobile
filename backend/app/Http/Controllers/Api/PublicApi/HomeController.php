<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Advertisement;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Shop;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->where('position', 'hero')
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        $categories = Category::with('children')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::with(['shop', 'primaryImage', 'brand'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->limit(8)
            ->get();

        $latestProducts = Product::with(['shop', 'primaryImage', 'brand'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $hotDeals = Product::with(['shop', 'primaryImage', 'brand'])
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->orderByRaw('((price - discount_price) / NULLIF(price, 0)) DESC')
            ->limit(8)
            ->get();

        $verifiedShops = Shop::where('status', 'approved')
            ->where('is_verified', true)
            ->orderBy('rating', 'desc')
            ->limit(8)
            ->get();

        $topRatedShops = Shop::where('status', 'approved')
            ->orderBy('rating', 'desc')
            ->limit(8)
            ->get();

        $flashSales = Offer::with(['shop', 'products.primaryImage'])
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->where('starts_at', '<=', now())
            ->orderBy('ends_at')
            ->limit(4)
            ->get();

        $announcements = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->get();

        $advertisements = Advertisement::with('shop')
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->get();

        return response()->json([
            'banners' => $banners,
            'categories' => $categories,
            'brands' => $brands,
            'featured_products' => $featuredProducts,
            'latest_products' => $latestProducts,
            'hot_deals' => $hotDeals,
            'verified_shops' => $verifiedShops,
            'top_rated_shops' => $topRatedShops,
            'flash_sales' => $flashSales,
            'announcements' => $announcements,
            'advertisements' => $advertisements,
        ]);
    }
}
