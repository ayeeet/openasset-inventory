<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     * Uses server-side query so the list always matches the database (no Livewire state).
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $categoryFilter = $request->query('category', '');
        $sortField = $request->query('sort_field', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');

        $categories = \App\Models\Asset::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category', 'category')
            ->sort();

        $assets = \App\Models\Asset::with(['location', 'assignedUser'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('serial_number', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%');
                });
            })
            ->when($categoryFilter !== '', function ($query) use ($categoryFilter) {
                $query->where('category', $categoryFilter);
            })
            ->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('assets.index', compact('assets', 'categories', 'search', 'categoryFilter', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = \App\Models\Location::all();
        $users = \App\Models\User::all();
        return view('assets.create', compact('locations', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|unique:assets',
            'category' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,maintenance,retired,lost',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
            'agreement' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('agreement')) {
            $validated['agreement'] = $request->file('agreement')->store('assets/documents', 'public');
        }

        

        $asset = \App\Models\Asset::create($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created Asset',
            'details' => "Created asset {$asset->name}",
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $asset->load(['location', 'assignedUser']);
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $locations = \App\Models\Location::all();
        $users = \App\Models\User::all();
        return view('assets.edit', compact('asset', 'locations', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|unique:assets,serial_number,' . $asset->id,
            'category' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,maintenance,retired,lost',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
            'agreement' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('agreement')) {
            if ($asset->agreement) {
                Storage::disk('public')->delete($asset->agreement);
            }
            $validated['agreement'] = $request->file('agreement')->store('assets/documents', 'public');
        }

        

        $asset->update($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Asset',
            'details' => "Updated asset {$asset->name}",
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        // Check if admin? Handled in policy normally, but let's check basic role
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($asset->agreement) {
            Storage::disk('public')->delete($asset->agreement);
        }

        $asset->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted Asset',
            'details' => "Deleted asset {$asset->name}",
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
