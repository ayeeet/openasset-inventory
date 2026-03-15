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

    public function breakdown($year, $month)
    {
        // Must be integer, month between 1 and 12
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        $resources = \App\Models\Resource::where('year', $year)->where('month', $month)->get()->map(function($item) {
            return [
                'id' => 'res_' . $item->id,
                'name' => $item->title . ($item->description ? ' (' . substr($item->description, 0, 30) . '...)' : ''),
                'type' => 'Resource (' . ucfirst($item->type) . ')',
                'amount' => (float) $item->amount,
            ];
        });

        $infra = \App\Models\InfrastructureCost::where('year', $year)->where('month', $month)->get()->map(function($item) {
            return [
                'id' => 'inf_' . $item->id,
                'name' => $item->service_name,
                'type' => 'Infrastructure',
                'amount' => (float) $item->amount,
            ];
        });

        $combined = $resources->concat($infra)->sortByDesc('amount')->values();
        $total = $combined->sum('amount');
        
        $combined = $combined->map(function($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['amount'] / $total) * 100, 1) : 0;
            return $item;
        });

        return response()->json([
            'year' => (int) $year,
            'month' => (int) $month,
            'total' => $total,
            'breakdown' => $combined
        ]);
    }
}
