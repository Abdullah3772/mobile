<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;

class DeliveryPartnerController extends Controller
{
    public function index()
    {
        $partners = DeliveryPartner::all();
        return response()->json(['delivery_partners' => $partners]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'coverage_areas' => 'nullable|string',
            'base_fee' => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['name', 'phone', 'email', 'coverage_areas', 'base_fee']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('delivery_partners', 'public');
        }

        $partner = DeliveryPartner::create($data);
        return response()->json(['message' => 'Delivery partner created', 'delivery_partner' => $partner], 201);
    }

    public function update(Request $request, DeliveryPartner $deliveryPartner)
    {
        $data = $request->only(['name', 'phone', 'email', 'coverage_areas', 'base_fee', 'is_active']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('delivery_partners', 'public');
        }

        $deliveryPartner->update($data);
        return response()->json(['message' => 'Updated', 'delivery_partner' => $deliveryPartner]);
    }

    public function destroy(DeliveryPartner $deliveryPartner)
    {
        $deliveryPartner->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
