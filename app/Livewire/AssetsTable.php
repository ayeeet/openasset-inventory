<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;

class AssetsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        // After creating/updating an asset, show full list so the new/updated item is visible
        if (session()->has('success')) {
            $this->search = '';
            $this->categoryFilter = '';
            $this->resetPage();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        // Get unique categories directly from assets
        $categories = Asset::select('category')->whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category', 'category')->sort();

        $assets = Asset::with(['location', 'assignedUser'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                      ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.assets-table', [
            'assets' => $assets,
            'categories' => $categories,
        ]);
    }
    
    public function delete($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        
        $asset = Asset::findOrFail($id);
        $asset->delete();
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted Asset',
            'details' => "Deleted asset {$asset->name}",
        ]);
        
        session()->flash('success', 'Asset deleted successfully.');
    }
}
