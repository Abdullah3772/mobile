<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
        }
        return $next($request);
    }
}
