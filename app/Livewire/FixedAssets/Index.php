<?php

declare(strict_types=1);

namespace App\Livewire\FixedAssets;

use App\Models\FixedAsset;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $category = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->authorize('fixed-assets.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getStatistics(): array
    {
        $branchId = auth()->user()->branch_id;

        return [
            'total_assets' => FixedAsset::where('branch_id', $branchId)->count(),
            'active_assets' => FixedAsset::where('branch_id', $branchId)->active()->count(),
            'total_value' => FixedAsset::where('branch_id', $branchId)->active()->sum('purchase_cost'),
            'total_book_value' => FixedAsset::where('branch_id', $branchId)->active()->sum('book_value'),
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $query = FixedAsset::where('branch_id', $branchId)
            ->with(['branch', 'supplier', 'assignedTo']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('asset_code', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $assets = $query->paginate(15);
        $statistics = $this->getStatistics();

        $categories = FixedAsset::where('branch_id', $branchId)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('livewire.fixed-assets.index', [
            'assets' => $assets,
            'statistics' => $statistics,
            'categories' => $categories,
        ]);
    }
}
