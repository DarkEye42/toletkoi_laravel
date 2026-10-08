<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedSearchController extends Controller
{
    public function index()
    {
        $savedSearches = SavedSearch::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('dashboard.saved-searches', compact('savedSearches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'filters' => 'required|array',
        ]);

        SavedSearch::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'filters' => $request->filters,
            'email_alerts' => $request->boolean('email_alerts'),
        ]);

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Search saved successfully']);
        }

        return back()->with('success', 'Search saved successfully!');
    }

    public function destroy(SavedSearch $savedSearch)
    {
        if ($savedSearch->user_id !== Auth::id()) {
            abort(403);
        }

        $savedSearch->delete();

        return back()->with('success', 'Saved search deleted!');
    }
}
