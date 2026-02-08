<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::with('department')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = \App\Models\Department::all();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:admin,manager,employee',
            'accessible_modules' => 'nullable|array',
            'accessible_modules.*' => 'string',
        ]);

        // Hash password
        $validated['password'] = bcrypt($validated['password']);

        $user = \App\Models\User::create($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created User',
            'details' => "Created user {$user->name}",
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->load(['department', 'assets', 'logs']);
        return view('users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $departments = \App\Models\Department::all();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:admin,manager,employee',
            'parent_password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
            'accessible_modules' => 'nullable|array',
            'accessible_modules.*' => 'string',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed']);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated User',
            'details' => "Updated user {$user->name}",
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Avoid deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        $user->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted User',
            'details' => "Deleted user {$user->name}",
        ]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
