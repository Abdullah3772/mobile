<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()
            ->with('product.primaryImage', 'product.shop', 'product.brand')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json($wishlists);
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $user = auth()->user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            Product::where('id', $request->product_id)->decrement('favorites_count');
            return response()->json(['message' => 'Removed from wishlist', 'wishlisted' => false]);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);
        Product::where('id', $request->product_id)->increment('favorites_count');

        return response()->json(['message' => 'Added to wishlist', 'wishlisted' => true]);
    }

    public function check($productId)
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();
        return response()->json(['wishlisted' => $exists]);
    }
}
