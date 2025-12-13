<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleGuardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_create_role_with_same_name_in_different_guards(): void
    {
        // Create a role with name "Manager" in the 'api' guard directly
        $apiRole = Role::create([
            'name' => 'Manager',
            'guard_name' => 'api',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Manager',
            'guard_name' => 'api',
        ]);

        // Authenticate as the test user
        Sanctum::actingAs($this->user);

        // POST to create "Manager" role via the admin endpoint (which should create it in 'web' guard)
        $response = $this->postJson('/api/v1/admin/roles', [
            'name' => 'Manager',
        ]);

        // Assert the response is successful (201 Created)
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
        ]);

        // Verify both roles exist in database with different guards
        $this->assertDatabaseHas('roles', [
            'name' => 'Manager',
            'guard_name' => 'api',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);

        // Verify there are exactly 2 roles named "Manager"
        $this->assertEquals(2, Role::where('name', 'Manager')->count());
    }

    public function test_admin_role_index_only_returns_web_guard_roles(): void
    {
        // Create roles in both guards
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        Role::create(['name' => 'API Admin', 'guard_name' => 'api']);
        Role::create(['name' => 'API Editor', 'guard_name' => 'api']);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/admin/roles');

        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        
        // Should only see web guard roles
        $this->assertCount(2, $data);
        
        $names = array_column($data, 'name');
        $this->assertContains('Admin', $names);
        $this->assertContains('Editor', $names);
        $this->assertNotContains('API Admin', $names);
        $this->assertNotContains('API Editor', $names);
    }

    public function test_admin_role_update_only_works_with_web_guard(): void
    {
        // Create a role in api guard
        $apiRole = Role::create(['name' => 'API Role', 'guard_name' => 'api']);
        
        // Create a role in web guard
        $webRole = Role::create(['name' => 'Web Role', 'guard_name' => 'web']);

        Sanctum::actingAs($this->user);

        // Try to update API role - should fail (404)
        $response = $this->putJson("/api/v1/admin/roles/{$apiRole->id}", [
            'name' => 'Updated API Role',
        ]);

        $response->assertStatus(404);

        // Update web role - should succeed
        $response = $this->putJson("/api/v1/admin/roles/{$webRole->id}", [
            'name' => 'Updated Web Role',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('roles', [
            'id' => $webRole->id,
            'name' => 'Updated Web Role',
            'guard_name' => 'web',
        ]);
    }

    public function test_admin_role_destroy_only_deletes_web_guard_roles(): void
    {
        // Create roles in both guards
        $apiRole = Role::create(['name' => 'API Role', 'guard_name' => 'api']);
        $webRole = Role::create(['name' => 'Web Role', 'guard_name' => 'web']);

        Sanctum::actingAs($this->user);

        // Try to delete API role - should not delete it
        $response = $this->deleteJson("/api/v1/admin/roles/{$apiRole->id}");

        $response->assertStatus(200); // Returns success but doesn't actually delete

        // API role should still exist
        $this->assertDatabaseHas('roles', [
            'id' => $apiRole->id,
            'guard_name' => 'api',
        ]);

        // Delete web role - should succeed
        $response = $this->deleteJson("/api/v1/admin/roles/{$webRole->id}");

        $response->assertStatus(200);

        // Web role should be deleted
        $this->assertDatabaseMissing('roles', [
            'id' => $webRole->id,
        ]);
    }

    public function test_role_name_uniqueness_scoped_to_guard(): void
    {
        // Create a role in web guard
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        Sanctum::actingAs($this->user);

        // Try to create another "Manager" in web guard - should fail
        $response = $this->postJson('/api/v1/admin/roles', [
            'name' => 'Manager',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_role_search_filters_by_guard_and_name(): void
    {
        // Create roles with similar names in both guards
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager Assistant', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'api']);
        Role::create(['name' => 'Editor', 'guard_name' => 'web']);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/admin/roles?q=Manager');

        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        
        // Should only return web guard roles matching "Manager"
        $this->assertCount(2, $data);
        
        $names = array_column($data, 'name');
        $this->assertContains('Manager', $names);
        $this->assertContains('Manager Assistant', $names);
        $this->assertNotContains('Editor', $names);
        
        // Verify all returned roles are web guard
        foreach ($data as $role) {
            $this->assertEquals('web', $role['guard_name']);
        }
    }
}
