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
        $review->update(['status' => 'rejected']);
        return response()->json(['message' => 'Review rejected', 'review' => $review]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }
}
