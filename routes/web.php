<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Health check endpoint for monitoring/load balancers
Route::get('/health', function () {
    try {
        // Check database connection
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        // Check cache
        \Illuminate\Support\Facades\Cache::get('test');
        
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
});

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('assets', AssetController::class)->middleware(['module:assets', 'module.write']);
    Route::resource('departments', DepartmentController::class)->middleware(['module:organization', 'module.write']);
    Route::resource('locations', LocationController::class)->middleware(['module:organization', 'module.write']);
    Route::resource('users', UserController::class)->middleware(['module:people', 'module.write']);
    Route::resource('logs', ActivityLogController::class)->only(['index', 'show'])->middleware('module:admin');
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index')->middleware('module:admin');
        Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update')->middleware('module:admin');
        
        Route::resource('resources', \App\Http\Controllers\ResourceController::class)->middleware(['module:resources', 'module.write']);
        Route::resource('budgets', \App\Http\Controllers\BudgetController::class)->middleware(['module:resources', 'module.write']);
    });
    
    // Admin routes only
    // You could wrap users/departments/locations in a middleware if required, 
    // but for now we'll allow all auth users to see, but maybe restrict actions in controller or policy.
    // Requirement says: "Admins can update status and delete assets", "Admins can CRUD both [Organization]", "Admins can activate/deactivate users".
    // We will handle authorization in Controllers or Policies.
});

require __DIR__.'/auth.php';
