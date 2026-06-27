<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPackage;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdPackageController extends Controller
{
    public function index()
    {
        $packages = AdPackage::all();
        return response()->json(['ad_packages' => $packages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'homepage_banner' => 'nullable|boolean',
            'top_search_placement' => 'nullable|boolean',
            'featured_badge' => 'nullable|boolean',
            'max_products' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $package = AdPackage::create($data);
        return response()->json(['message' => 'Package created', 'ad_package' => $package], 201);
    }

    public function update(Request $request, AdPackage $adPackage)
    {
        $data = $request->all();
        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $adPackage->update($data);
        return response()->json(['message' => 'Updated', 'ad_package' => $adPackage]);
    }

    public function destroy(AdPackage $adPackage)
    {
        $adPackage->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function advertisements(Request $request)
    {
        $query = Advertisement::with(['shop', 'adPackage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $ads = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($ads);
    }

    public function approveAd(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'active']);
        return response()->json(['message' => 'Advertisement approved']);
    }

    public function rejectAd(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'rejected']);
        return response()->json(['message' => 'Advertisement rejected']);
    }
}
