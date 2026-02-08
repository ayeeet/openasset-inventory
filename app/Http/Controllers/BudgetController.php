<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::orderBy('year', 'desc')->get();
        return view('resources.budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('resources.budgets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min(2000)|max(2100)|unique:budgets,year',
            'monthly_budget' => 'required|numeric|min(0)',
            'annual_budget' => 'required|numeric|min(0)',
        ]);

        Budget::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        return view('resources.budgets.edit', compact('budget'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'monthly_budget' => 'required|numeric|min(0)',
            'annual_budget' => 'required|numeric|min(0)',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();
        return back()->with('success', 'Budget deleted.');
    }
}
