<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'shop', 'product']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($reviews);
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        $shop = $review->shop;
        $avgRating = Review::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->avg('rating');
        $totalReviews = Review::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->count();

        $shop->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);

        return response()->json(['message' => 'Review approved', 'review' => $review]);
    }

    public function reject(Review $review)
    {
        $wasApproved = $review->status === 'approved';
        $review->update(['status' => 'rejected']);

        if ($wasApproved) {
            $this->recalculateShopRating($review->shop);
        }

        return response()->json(['message' => 'Review rejected', 'review' => $review]);
    }

    public function destroy(Review $review)
    {
        $wasApproved = $review->status === 'approved';
        $shop = $review->shop;
        $review->delete();

        if ($wasApproved) {
            $this->recalculateShopRating($shop);
        }

        return response()->json(['message' => 'Review deleted']);
    }

    private function recalculateShopRating($shop)
    {
        $avgRating = Review::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->avg('rating');
        $totalReviews = Review::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->count();

        $shop->update([
            'rating' => round($avgRating ?? 0, 2),
            'total_reviews' => $totalReviews,
        ]);
    }
}
