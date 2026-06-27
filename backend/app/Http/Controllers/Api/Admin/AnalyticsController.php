<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_shops' => Shop::where('status', 'approved')->count(),
            'total_products' => Product::where('status', 'active')->count(),
            'total_reservations' => Reservation::count(),
            'pending_shops' => Shop::where('status', 'pending')->count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_products_today' => Product::whereDate('created_at', today())->count(),
        ]);
    }

    public function topShops()
    {
        $shops = Shop::where('status', 'approved')
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();
        return response()->json(['top_shops' => $shops]);
    }

    public function mostViewedProducts()
    {
        $products = Product::with(['shop', 'primaryImage'])
            ->where('status', 'active')
            ->orderBy('views_count', 'desc')
            ->limit(20)
            ->get();
        return response()->json(['most_viewed' => $products]);
    }

    public function recentReservations()
    {
        $reservations = Reservation::with(['user', 'product', 'shop'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        return response()->json(['recent_reservations' => $reservations]);
    }

    public function userStats()
    {
        return response()->json([
            'total' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'shop_owners' => User::where('role', 'shop_owner')->count(),
            'admins' => User::where('role', 'super_admin')->count(),
            'active' => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ]);
    }
}
