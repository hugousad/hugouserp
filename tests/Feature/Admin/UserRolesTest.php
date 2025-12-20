<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users\Form;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_revoked_when_none_selected(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $target = User::factory()->create(['branch_id' => $branch->id]);

        $managePermission = Permission::findOrCreate('users.manage', 'web');
        $admin->givePermissionTo($managePermission);

        $role = Role::findOrCreate('editor', 'web');
        $target->syncRoles([$role]);

        $this->actingAs($admin);

        Livewire::test(Form::class, ['user' => $target->id])
            ->set('form.name', $target->name)
            ->set('form.email', $target->email)
            ->set('form.branch_id', $branch->id)
            ->set('form.locale', 'en')
            ->set('form.timezone', 'UTC')
            ->set('form.password', '')
            ->set('form.password_confirmation', '')
            ->set('selectedRoles', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(
            $target->fresh()->roles()->where('guard_name', 'web')->get()->isEmpty()
        );
    }
}
