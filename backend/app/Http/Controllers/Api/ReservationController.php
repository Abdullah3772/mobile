<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with(['product.primaryImage', 'shop'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return response()->json(['error' => 'Product is not available'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'pickup_date' => $request->pickup_date,
            'notes' => $request->notes,
        ]);

        $product->increment('reservations_count');

        Notification::create([
            'user_id' => $product->shop->user_id,
            'title' => 'New Reservation',
            'message' => "New reservation for '{$product->title}' by {$request->customer_name}",
            'type' => 'reservation',
            'link' => "/shop/reservations",
        ]);

        return response()->json([
            'message' => 'Reservation placed successfully',
            'reservation' => $reservation->load(['product', 'shop']),
        ], 201);
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json(['error' => 'Cannot cancel this reservation'], 400);
        }

        $reservation->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Reservation cancelled']);
    }
}
