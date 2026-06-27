<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['shop', 'category', 'brand', 'primaryImage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($products);
    }

    public function feature(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        $status = $product->is_featured ? 'featured' : 'unfeatured';
        return response()->json(['message' => "Product {$status}", 'product' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
