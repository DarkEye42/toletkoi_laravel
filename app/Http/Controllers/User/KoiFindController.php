<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\KoiFindRequest as KoiFindFormRequest;
use App\Models\KoifindRequest;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KoiFindController extends Controller
{
    public function index()
    {
        $requests = KoifindRequest::where('user_id', Auth::id())
            ->with('candidates')
            ->latest()
            ->paginate(10);

        $propertyTypes = PropertyType::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('dashboard.koifind.index', compact('requests', 'propertyTypes', 'divisions'));
    }

    public function store(KoiFindFormRequest $request)
    {
        $validated = $request->validated();

        $koifind = KoifindRequest::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'property_type_id' => $validated['property_type_id'] ?? null,
            'listing_type' => $validated['listing_type'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
            'bedrooms' => $validated['bedrooms'] ?? null,
            'preferred_areas' => $validated['preferred_areas'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard.koifind.show', $koifind)
            ->with('success', 'KoiFind request created! Agents will contact you with matching properties.');
    }

    public function show(KoifindRequest $koifindRequest)
    {
        if ($koifindRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $koifindRequest->load('candidates.serviceProvider');

        return view('dashboard.koifind.show', ['koifind' => $koifindRequest]);
    }
}
