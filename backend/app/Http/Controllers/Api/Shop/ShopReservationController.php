<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ShopReservationController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;

        if (!$shop || !$shop->isApproved()) {
            return response()->json(['error' => 'Shop not approved'], 403);
        }

        $query = $shop->reservations()->with(['user', 'product.primaryImage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($reservations);
    }

    public function accept(Reservation $reservation)
    {
        $shop = auth()->user()->shop;

        if ($reservation->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reservation->update(['status' => 'confirmed']);

        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Reservation Confirmed',
            'message' => "Your reservation for '{$reservation->product->title}' has been confirmed!",
            'type' => 'reservation',
            'link' => "/reservations/{$reservation->id}",
        ]);

        return response()->json(['message' => 'Reservation confirmed', 'reservation' => $reservation]);
    }

    public function reject(Request $request, Reservation $reservation)
    {
        $shop = auth()->user()->shop;

        if ($reservation->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['rejection_reason' => 'nullable|string']);

        $reservation->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Reservation Rejected',
            'message' => "Your reservation for '{$reservation->product->title}' has been rejected.",
            'type' => 'reservation',
        ]);

        return response()->json(['message' => 'Reservation rejected']);
    }

    public function complete(Reservation $reservation)
    {
        $shop = auth()->user()->shop;

        if ($reservation->shop_id !== $shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reservation->update(['status' => 'completed']);
        return response()->json(['message' => 'Reservation completed']);
    }
}
