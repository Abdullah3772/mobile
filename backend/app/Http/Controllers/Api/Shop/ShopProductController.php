<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopProductController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;

        if (!$shop || !$shop->isApproved()) {
            return response()->json(['error' => 'Shop not approved'], 403);
        }

        $query = $shop->products()->with(['category', 'brand', 'primaryImage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        if (!$shop || !$shop->isApproved()) {
            return response()->json(['error' => 'Shop not approved'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'model' => 'nullable|string|max:255',
            'condition' => 'required|in:brand_new,used,refurbished',
            'storage' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string',
            'warranty_type' => 'nullable|string',
            'network_type' => 'nullable|string',
            'imei' => 'nullable|string|max:20',
            'trcsl_approved' => 'nullable|boolean',
            'box_available' => 'nullable|boolean',
            'accessories_included' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'battery_health' => 'nullable|string',
            'scratches' => 'nullable|string',
            'face_id_working' => 'nullable|boolean',
            'original_display' => 'nullable|boolean',
            'repair_history' => 'nullable|string',
            'cash_price' => 'nullable|numeric|min:0',
            'card_price' => 'nullable|numeric|min:0',
            'emi_available' => 'nullable|boolean',
            'camera' => 'nullable|string',
            'battery' => 'nullable|string',
            'processor' => 'nullable|string',
            'screen_size' => 'nullable|string',
            'five_g_support' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
        ]);

        $data = $request->except(['images', 'video']);
        $data['shop_id'] = $shop->id;
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(6);

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $image->store('products', 'public'),
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($request->hasFile('video')) {
            ProductVideo::create([
                'product_id' => $product->id,
                'video_path' => $request->file('video')->store('products/videos', 'public'),
            ]);
        }

        $shop->increment('total_products');

        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product->load(['images', 'videos', 'category', 'brand']),
        ], 201);
    }

    public function show(Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->load(['images', 'videos', 'category', 'brand', 'reservations']);
        return response()->json(['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->only([
            'title', 'category_id', 'brand_id', 'description', 'model',
            'condition', 'storage', 'ram', 'color', 'price', 'discount_price',
            'warranty', 'warranty_type', 'network_type', 'imei', 'trcsl_approved',
            'box_available', 'accessories_included', 'stock_quantity',
            'battery_health', 'scratches', 'face_id_working', 'original_display',
            'repair_history', 'cash_price', 'card_price', 'emi_available',
            'camera', 'battery', 'processor', 'screen_size', 'five_g_support',
        ]);

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(6);
        }

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $image->store('products', 'public'),
                    'sort_order' => $product->images()->count() + $index,
                ]);
            }
        }

        return response()->json([
            'message' => 'Product updated',
            'product' => $product->load(['images', 'videos', 'category', 'brand']),
        ]);
    }

    public function destroy(Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->delete();
        $shop->decrement('total_products');

        return response()->json(['message' => 'Product deleted']);
    }

    public function markSold(Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->update(['status' => 'sold']);
        return response()->json(['message' => 'Product marked as sold']);
    }

    public function updateStock(Request $request, Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['stock_quantity' => 'required|integer|min:0']);
        $product->update(['stock_quantity' => $request->stock_quantity]);

        return response()->json(['message' => 'Stock updated', 'product' => $product]);
    }
}
