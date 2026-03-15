<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Resource;
use App\Models\InfrastructureCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfrastructureCostController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $costs = InfrastructureCost::where('year', $year)
            ->with('creator')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $budget = Budget::where('year', $year)->first();
        
        $totalInfraSpent = InfrastructureCost::where('year', $year)->sum('amount');
        
        $monthlySpent = InfrastructureCost::where('year', $year)
            ->select('month', DB::raw('sum(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $totalOverallSpent = Resource::where('year', $year)->sum('amount') + $totalInfraSpent;

        return view('infrastructure_costs.index', compact('costs', 'budget', 'year', 'totalInfraSpent', 'monthlySpent', 'totalOverallSpent'));
    }

    public function create()
    {
        return view('infrastructure_costs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string',
        ]);
        
        $budget = Budget::where('year', $validated['year'])->first();
        
        if (!$budget) {
            return back()->withErrors(['amount' => 'No budget defined for this year. Please contact admin.'])->withInput();
        }

        $currentYearlySpent = Resource::where('year', $validated['year'])->sum('amount') 
                            + InfrastructureCost::where('year', $validated['year'])->sum('amount');
                            
        if (($currentYearlySpent + $validated['amount']) > $budget->annual_budget) {
             return back()->withErrors(['amount' => 'This expense exceeds the remaining annual budget.'])->withInput();
        }

        InfrastructureCost::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('infrastructure-costs.index', ['year' => $validated['year']])->with('success', 'Infrastructure cost logged successfully.');
    }

    public function edit(InfrastructureCost $infrastructureCost)
    {
        return view('infrastructure_costs.edit', compact('infrastructureCost'));
    }

    public function update(Request $request, InfrastructureCost $infrastructureCost)
    {
         $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string',
        ]);
        
        $budget = Budget::where('year', $validated['year'])->first();
        if (!$budget) {
            return back()->withErrors(['amount' => 'No budget defined for this year.'])->withInput();
        }
        
        $currentYearlySpent = Resource::where('year', $validated['year'])->sum('amount') 
                            + InfrastructureCost::where('year', $validated['year'])->where('id', '!=', $infrastructureCost->id)->sum('amount');
                            
        if (($currentYearlySpent + $validated['amount']) > $budget->annual_budget) {
             return back()->withErrors(['amount' => 'Update exceeds annual budget.'])->withInput();
        }

        $infrastructureCost->update($validated);

        return redirect()->route('infrastructure-costs.index', ['year' => $validated['year']])->with('success', 'Infrastructure cost updated successfully.');
    }

    public function destroy(InfrastructureCost $infrastructureCost)
    {
        $infrastructureCost->delete();
        return back()->with('success', 'Entry deleted.');
    }
}
