<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopFollowController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $user = auth()->user();
        $shopId = $request->shop_id;

        if ($user->followedShops()->where('shop_id', $shopId)->exists()) {
            $user->followedShops()->detach($shopId);
            return response()->json(['message' => 'Unfollowed', 'following' => false]);
        }

        $user->followedShops()->attach($shopId);
        return response()->json(['message' => 'Following', 'following' => true]);
    }

    public function following()
    {
        $shops = auth()->user()->followedShops()->paginate(15);
        return response()->json($shops);
    }

    public function check($shopId)
    {
        $following = auth()->user()->followedShops()->where('shop_id', $shopId)->exists();
        return response()->json(['following' => $following]);
    }
}
