<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Store;

use App\Livewire\Admin\Store\Stores;
use App\Models\Branch;
use App\Models\BranchModule;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class StoreModulesFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_sync_modules_only_include_item_enabled_branch_modules(): void
    {
        $branch = Branch::factory()->create();
        $itemModule = Module::factory()->create([
            'supports_items' => true,
            'has_inventory' => true,
        ]);
        $nonItemModule = Module::factory()->create([
            'supports_items' => false,
            'has_inventory' => true,
        ]);

        BranchModule::create([
            'branch_id' => $branch->id,
            'module_id' => $itemModule->id,
            'module_key' => $itemModule->key,
            'enabled' => true,
        ]);

        BranchModule::create([
            'branch_id' => $branch->id,
            'module_id' => $nonItemModule->id,
            'module_key' => $nonItemModule->key,
            'enabled' => true,
        ]);

        $user = $this->userWithStorePermission($branch);
        $this->actingAs($user);

        Livewire::test(Stores::class)
            ->set('branch_id', $branch->id)
            ->assertViewHas('modules', function ($modules) use ($itemModule, $nonItemModule) {
                return $modules->contains('id', $itemModule->id)
                    && ! $modules->contains('id', $nonItemModule->id);
            });
    }

    protected function userWithStorePermission(Branch $branch): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('stores.view', 'web');

        $user = User::factory()->create(['branch_id' => $branch->id]);
        $user->givePermissionTo(['stores.view']);

        return $user;
    }
}
