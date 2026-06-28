<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Reservation;

class ShopAnalyticsController extends Controller
{
    public function dashboard()
    {
        $shop = auth()->user()->shop;

        if (!$shop || !$shop->isApproved()) {
            return response()->json(['error' => 'Shop not approved'], 403);
        }

        $totalViews = $shop->products()->sum('views_count');
        $totalFavorites = $shop->products()->sum('favorites_count');
        $totalReservations = $shop->reservations()->count();
        $completedSales = $shop->reservations()->where('status', 'completed')->count();
        $activeProducts = $shop->products()->where('status', 'active')->count();
        $soldProducts = $shop->products()->where('status', 'sold')->count();

        return response()->json([
            'total_views' => $totalViews,
            'total_favorites' => $totalFavorites,
            'total_reservations' => $totalReservations,
            'completed_sales' => $completedSales,
            'active_products' => $activeProducts,
            'sold_products' => $soldProducts,
            'shop_rating' => $shop->rating,
            'total_reviews' => $shop->total_reviews,
            'total_followers' => $shop->followers()->count(),
        ]);
    }
}
