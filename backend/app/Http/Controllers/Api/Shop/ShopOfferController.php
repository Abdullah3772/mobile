<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopOfferController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        $offers = $shop->offers()->with('products.primaryImage')->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($offers);
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:flash_sale,weekend_offer,clearance_sale,festival_offer,custom',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'banner_image' => 'nullable|image|max:5120',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $data = $request->except(['banner_image', 'product_ids']);
        $data['shop_id'] = $shop->id;
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('offers', 'public');
        }

        $offer = Offer::create($data);

        if ($request->has('product_ids')) {
            $offer->products()->attach($request->product_ids);
        }

        return response()->json([
            'message' => 'Offer created',
            'offer' => $offer->load('products'),
        ], 201);
    }

    public function update(Request $request, Offer $offer)
    {
        $shop = auth()->user()->shop;

        if ($offer->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->except(['banner_image', 'product_ids']);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('offers', 'public');
        }

        $offer->update($data);

        if ($request->has('product_ids')) {
            $offer->products()->sync($request->product_ids);
        }

        return response()->json(['message' => 'Offer updated', 'offer' => $offer->load('products')]);
    }

    public function destroy(Offer $offer)
    {
        $shop = auth()->user()->shop;

        if ($offer->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $offer->delete();
        return response()->json(['message' => 'Offer deleted']);
    }
}
