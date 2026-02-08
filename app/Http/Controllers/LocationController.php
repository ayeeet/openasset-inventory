<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = \App\Models\Location::paginate(10);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $location = \App\Models\Location::create($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created Location',
            'details' => "Created location {$location->name}",
        ]);

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function show(string $id)
    {
        $location = \App\Models\Location::findOrFail($id);
        return view('locations.show', compact('location'));
    }

    public function edit(string $id)
    {
        $location = \App\Models\Location::findOrFail($id);
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, string $id)
    {
        $location = \App\Models\Location::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $location->update($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Location',
            'details' => "Updated location {$location->name}",
        ]);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(string $id)
    {
        $location = \App\Models\Location::findOrFail($id);
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $location->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted Location',
            'details' => "Deleted location {$location->name}",
        ]);

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
