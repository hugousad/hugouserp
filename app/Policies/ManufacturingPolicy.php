<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BillOfMaterial;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Models\WorkCenter;
use App\Policies\Concerns\ChecksPermissions;

class ManufacturingPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->has($user, 'manufacturing.view');
    }

    public function view(User $user, mixed $model): bool
    {
        return $this->has($user, 'manufacturing.view');
    }

    public function create(User $user): bool
    {
        return $this->has($user, 'manufacturing.create');
    }

    public function update(User $user, mixed $model): bool
    {
        return $this->has($user, 'manufacturing.edit');
    }

    public function delete(User $user, mixed $model): bool
    {
        return $this->has($user, 'manufacturing.delete');
    }

    public function approve(User $user, mixed $model): bool
    {
        return $this->has($user, 'manufacturing.approve');
    }

    // BOM-specific methods
    public function viewBom(User $user): bool
    {
        return $this->has($user, 'manufacturing.view');
    }

    public function createBom(User $user): bool
    {
        return $this->has($user, 'manufacturing.create');
    }

    public function updateBom(User $user, BillOfMaterial $bom): bool
    {
        return $this->has($user, 'manufacturing.edit');
    }

    public function deleteBom(User $user, BillOfMaterial $bom): bool
    {
        return $this->has($user, 'manufacturing.delete');
    }

    // Production Order-specific methods
    public function viewProductionOrder(User $user): bool
    {
        return $this->has($user, 'manufacturing.view');
    }

    public function createProductionOrder(User $user): bool
    {
        return $this->has($user, 'manufacturing.create');
    }

    public function updateProductionOrder(User $user, ProductionOrder $order): bool
    {
        return $this->has($user, 'manufacturing.edit');
    }

    public function deleteProductionOrder(User $user, ProductionOrder $order): bool
    {
        return $this->has($user, 'manufacturing.delete');
    }

    public function approveProductionOrder(User $user, ProductionOrder $order): bool
    {
        return $this->has($user, 'manufacturing.approve');
    }

    // Work Center-specific methods
    public function viewWorkCenter(User $user): bool
    {
        return $this->has($user, 'manufacturing.view');
    }

    public function createWorkCenter(User $user): bool
    {
        return $this->has($user, 'manufacturing.create');
    }

    public function updateWorkCenter(User $user, WorkCenter $workCenter): bool
    {
        return $this->has($user, 'manufacturing.edit');
    }

    public function deleteWorkCenter(User $user, WorkCenter $workCenter): bool
    {
        return $this->has($user, 'manufacturing.delete');
    }
}
