<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RentalInvoice;
use App\Repositories\Contracts\RentalInvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class RentalInvoiceRepository extends EloquentBaseRepository implements RentalInvoiceRepositoryInterface
{
    public function __construct(RentalInvoice $model)
    {
        parent::__construct($model);
    }

    protected function baseBranchQuery(int $branchId): Builder
    {
        // Use the contract relationship for branch filtering instead of non-existent branch_id column
        return $this->query()->forBranch($branchId);
    }

    public function paginateForBranch(int $branchId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->baseBranchQuery($branchId)
            ->with('contract') // Eager load contracts to prevent N+1
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
