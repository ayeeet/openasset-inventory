<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $resources = Resource::where('year', $year)
            ->with('creator')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $budget = Budget::where('year', $year)->first();
        
        $totalSpent = Resource::where('year', $year)->sum('amount');
        
        $monthlySpent = Resource::where('year', $year)
            ->select('month', DB::raw('sum(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return view('resources.index', compact('resources', 'budget', 'year', 'totalSpent', 'monthlySpent'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('resources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min(0)',
            'type' => 'required|in:invoice,expense',
            'month' => 'required|integer|min(1)|max(12)',
            'year' => 'required|integer|min(2000)|max(2100)',
            'description' => 'nullable|string',
        ]);

        // Check Budget
        $budget = Budget::where('year', $validated['year'])->first();
        
        if (!$budget) {
            return back()->withErrors(['amount' => 'No budget defined for this year. Please contact admin.'])->withInput();
        }

        $currentYearlySpent = Resource::where('year', $validated['year'])->sum('amount');
        if (($currentYearlySpent + $validated['amount']) > $budget->annual_budget) {
             return back()->withErrors(['amount' => 'This expense exceeds the remaining annual budget.'])->withInput();
        }

        $currentMonthlySpent = Resource::where('year', $validated['year'])->where('month', $validated['month'])->sum('amount');
         // Optional: Check monthly budget strictness. For now, we only warn or let it slide if annual is ok? 
         // Requirement says "Ensure spending aligns with the budget". Let's enforce annual strictly, and monthly as a warning or check if requested.
         // Let's enforce ONLY annual budget hard limit for now based on typical requirements unless specified "strict monthly".
         // But wait, "Validate that monthly AND annual totals do not exceed". Okay, let's check annual. Monthly might be flexible if annual is fine? 
         // "Validate that monthly and annual totals do not exceed the allocated budget." -> This implies monthly budget is also a hard limit?
         // If "monthly_budget" in budgets table is a single value, does it apply to ALL months? Yes.
         
         if (($currentMonthlySpent + $validated['amount']) > $budget->monthly_budget) {
              return back()->withErrors(['amount' => 'This expense exceeds the monthly budget limit.'])->withInput();
         }

        Resource::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('resources.index', ['year' => $validated['year']])->with('success', 'Resource entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        return view('resources.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
         $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min(0)',
            'type' => 'required|in:invoice,expense',
            'month' => 'required|integer|min(1)|max(12)',
            'year' => 'required|integer|min(2000)|max(2100)',
            'description' => 'nullable|string',
        ]);
        
        // Similar validation logic re-check difference
         $budget = Budget::where('year', $validated['year'])->first();
         if (!$budget) {
            return back()->withErrors(['amount' => 'No budget defined for this year.'])->withInput();
        }
        
        // Exclude current resource amount from sums
        $currentYearlySpent = Resource::where('year', $validated['year'])->where('id', '!=', $resource->id)->sum('amount');
        if (($currentYearlySpent + $validated['amount']) > $budget->annual_budget) {
             return back()->withErrors(['amount' => 'Update exceeds annual budget.'])->withInput();
        }
        
        $currentMonthlySpent = Resource::where('year', $validated['year'])->where('month', $validated['month'])->where('id', '!=', $resource->id)->sum('amount');
        if (($currentMonthlySpent + $validated['amount']) > $budget->monthly_budget) {
              return back()->withErrors(['amount' => 'Update exceeds monthly budget.'])->withInput();
         }

        $resource->update($validated);

        return redirect()->route('resources.index', ['year' => $validated['year']])->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return back()->with('success', 'Entry deleted.');
    }
}
