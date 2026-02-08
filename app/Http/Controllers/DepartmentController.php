<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = \App\Models\Department::with(['location', 'head'])->paginate(10);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $locations = \App\Models\Location::all();
        return view('departments.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'head_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $department = \App\Models\Department::create($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created Department',
            'details' => "Created department {$department->name}",
        ]);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function show(string $id)
    {
        $department = \App\Models\Department::findOrFail($id);
        $department->load(['location', 'head', 'users']);
        return view('departments.show', compact('department'));
    }

    public function edit(string $id)
    {
        $department = \App\Models\Department::findOrFail($id);
        $locations = \App\Models\Location::all();
        return view('departments.edit', compact('department', 'locations'));
    }

    public function update(Request $request, string $id)
    {
        $department = \App\Models\Department::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'head_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $department->update($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Department',
            'details' => "Updated department {$department->name}",
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(string $id)
    {
        $department = \App\Models\Department::findOrFail($id);
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $department->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted Department',
            'details' => "Deleted department {$department->name}",
        ]);

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
