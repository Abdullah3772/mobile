<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string',
            'nic' => 'required|string|max:20',
            'address' => 'required|string',
            'district' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'google_map_lat' => 'nullable|string',
            'google_map_lng' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:5120',
            'about' => 'nullable|string',
            'opening_hours' => 'nullable|json',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:5120',
        ]);

        $user = auth()->user();

        if ($user->shop) {
            return response()->json(['error' => 'You already have a registered shop'], 400);
        }

        $data = $request->only([
            'name', 'owner_name', 'business_registration_number', 'nic',
            'address', 'district', 'phone', 'whatsapp', 'email',
            'google_map_lat', 'google_map_lng', 'about',
        ]);

        $data['user_id'] = $user->id;
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);

        if ($request->has('opening_hours')) {
            $data['opening_hours'] = json_decode($request->opening_hours, true);
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('shops/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('shops/covers', 'public');
        }

        $shop = Shop::create($data);

        $user->update(['role' => 'shop_owner']);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                ShopDocument::create([
                    'shop_id' => $shop->id,
                    'title' => $doc->getClientOriginalName(),
                    'file_path' => $doc->store('shops/documents', 'public'),
                    'file_type' => $doc->getClientMimeType(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Shop registered successfully. Awaiting admin approval.',
            'shop' => $shop->load('documents'),
        ], 201);
    }

    public function myShop()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return response()->json(['error' => 'No shop found'], 404);
        }

        $shop->load('documents');
        return response()->json(['shop' => $shop]);
    }

    public function updateShop(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return response()->json(['error' => 'No shop found'], 404);
        }

        $data = $request->only([
            'name', 'owner_name', 'address', 'district', 'phone', 'whatsapp',
            'email', 'google_map_lat', 'google_map_lng', 'about',
        ]);

        if ($request->has('opening_hours')) {
            $data['opening_hours'] = json_decode($request->opening_hours, true);
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('shops/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('shops/covers', 'public');
        }

        $shop->update($data);
        return response()->json(['message' => 'Shop updated', 'shop' => $shop]);
    }
}
