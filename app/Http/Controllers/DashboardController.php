<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isEmployee()) {
            return redirect()->route(auth()->user()->homeRouteName());
        }

        $totalAssets = \App\Models\Asset::count();
        $assignedAssets = \App\Models\Asset::whereNotNull('assigned_to_user_id')->count();
        $unassignedAssets = \App\Models\Asset::whereNull('assigned_to_user_id')->where('status', '!=', 'retired')->count();
        $retiredAssets = \App\Models\Asset::where('status', 'retired')->count();
        
        $totalUsers = \App\Models\User::count();
        $totalDepartments = \App\Models\Department::count();
        $totalLocations = \App\Models\Location::count();

        $recentLogs = \App\Models\ActivityLog::with('user')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalAssets', 
            'assignedAssets', 
            'unassignedAssets', 
            'retiredAssets',
            'totalUsers',
            'totalDepartments',
            'totalLocations',
            'recentLogs'
        ));
    }
}
