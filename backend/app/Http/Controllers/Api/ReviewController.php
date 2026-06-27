<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $existing = Review::where('user_id', auth()->id())
            ->where('shop_id', $request->shop_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You have already reviewed this shop'], 400);
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'shop_id' => $request->shop_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review submitted for approval', 'review' => $review], 201);
    }

    public function shopReviews($shopId)
    {
        $reviews = Review::with('user')
            ->where('shop_id', $shopId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($reviews);
    }
}
