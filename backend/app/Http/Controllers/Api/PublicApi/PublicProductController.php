<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['shop', 'category', 'brand', 'primaryImage'])
            ->where('status', 'active')
            ->whereHas('shop', function ($q) {
                $q->where('status', 'approved');
            });

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->has('district')) {
            $query->whereHas('shop', function ($q) use ($request) {
                $q->where('district', $request->district);
            });
        }
        if ($request->has('ram')) {
            $query->where('ram', $request->ram);
        }
        if ($request->has('storage')) {
            $query->where('storage', $request->storage);
        }
        if ($request->has('warranty')) {
            $query->whereNotNull('warranty');
        }
        if ($request->has('battery_health')) {
            $query->where('battery_health', '>=', $request->battery_health);
        }
        if ($request->boolean('five_g_support')) {
            $query->where('five_g_support', true);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'desc';
        $allowedSorts = ['price', 'created_at', 'views_count', 'favorites_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $products = $query->paginate(20);
        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::with(['shop', 'category', 'brand', 'images', 'videos'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $product->increment('views_count');

        $related = Product::with(['primaryImage', 'shop'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(8)
            ->get();

        return response()->json([
            'product' => $product,
            'related_products' => $related,
        ]);
    }

    public function featured()
    {
        $products = Product::with(['shop', 'primaryImage', 'brand'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->limit(12)
            ->get();
        return response()->json(['featured_products' => $products]);
    }

    public function latest()
    {
        $products = Product::with(['shop', 'primaryImage', 'brand'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
        return response()->json(['latest_products' => $products]);
    }

    public function compare(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:2|max:4',
            'product_ids.*' => 'exists:products,id',
        ]);

        $products = Product::with(['shop', 'images', 'brand', 'category'])
            ->whereIn('id', $request->product_ids)
            ->get();

        return response()->json(['products' => $products]);
    }
}
