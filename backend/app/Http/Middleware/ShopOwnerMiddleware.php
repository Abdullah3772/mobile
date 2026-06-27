<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopOwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->role !== 'shop_owner' && $user->role !== 'super_admin') {
            return response()->json(['error' => 'Unauthorized. Shop owner access required.'], 403);
        }
        return $next($request);
    }
}
