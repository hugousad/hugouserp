<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserSecretsMassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('users.manage', 'web');

        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('users.manage');
    }

    public function test_user_creation_ignores_sensitive_fields(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/users', [
            'name' => 'Alice',
            'email' => 'alice@example.test',
            'password' => 'Secret123!',
            'two_factor_secret' => 'leaked-secret',
            'two_factor_recovery_codes' => ['bad-code'],
        ]);

        $response->assertCreated();

        $user = User::where('email', 'alice@example.test')->firstOrFail();

        $this->assertTrue(Hash::check('Secret123!', $user->password));
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
    }

    public function test_user_update_does_not_allow_mass_assigning_auth_secrets(): void
    {
        $user = User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.test',
            'password' => Hash::make('OldPass!'),
        ]);
        $user->two_factor_secret = 'original-secret';
        $user->two_factor_recovery_codes = ['existing'];
        $user->save();

        $response = $this->actingAs($this->admin, 'sanctum')->patchJson('/api/v1/admin/users/'.$user->id, [
            'name' => 'Bob Updated',
            'password' => 'NewPass!23',
            'two_factor_secret' => 'hacked',
            'two_factor_recovery_codes' => ['hacked'],
        ]);

        $response->assertOk();

        $user->refresh();

        $this->assertEquals('Bob Updated', $user->name);
        $this->assertTrue(Hash::check('NewPass!23', $user->password));
        $this->assertEquals('original-secret', $user->two_factor_secret);
        $this->assertEquals(json_encode(['existing']), $user->two_factor_recovery_codes);
    }
}
