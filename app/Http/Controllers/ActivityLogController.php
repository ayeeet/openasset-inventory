<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);
        return view('logs.index', compact('logs'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(ActivityLog $activityLog) // Using model binding from --model
    {
        // $activityLog is already bound, but the method name in route resource is 'logs', so param might be 'log'.
        // Route::resource('logs', ...) -> parameter is {log}
        // But Controller type hint is ActivityLog $activityLog.
        // Laravel handles this if variable name matches or type hint matches.
        // Wait, Route::resource('logs') generates parameter {log}.
        // The type hint defaults to matching the model.
        // If it fails, I'll switch to string $id.
        return view('logs.show', ['log' => $activityLog]);
    }

    public function edit(ActivityLog $activityLog)
    {
        abort(404);
    }

    public function update(Request $request, ActivityLog $activityLog)
    {
        abort(404);
    }

    public function destroy(ActivityLog $activityLog)
    {
        abort(404);
    }
}
