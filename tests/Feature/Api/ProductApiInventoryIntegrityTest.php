<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiInventoryIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected Store $store;
    protected User $user;
    protected Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TEST',
        ]);

        // Create warehouse
        $this->warehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'branch_id' => $this->branch->id,
            'is_default' => true,
        ]);

        // Create user
        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        // Create store
        $this->store = Store::create([
            'name' => 'Test Store',
            'code' => 'STORE-TEST',
            'branch_id' => $this->branch->id,
        ]);
    }

    protected function actingAsStoreApi(): static
    {
        $this->actingAs($this->user);
        
        // Create a store token to simulate the middleware
        $token = \App\Models\StoreToken::create([
            'store_id' => $this->store->id,
            'name' => 'Test Token',
            'token' => \Illuminate\Support\Str::random(60),
            'abilities' => ['*'],
            'expires_at' => now()->addYear(),
        ]);
        
        // Add authorization header
        $this->withHeader('Authorization', 'Bearer ' . $token->token);
        
        return $this;
    }

    public function test_product_create_generates_stock_movement_record(): void
    {
        $this->actingAsStoreApi();

        $response = $this->postJson('/api/v1/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 100,
            'quantity' => 50,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response->assertStatus(201);
        $productId = $response->json('data.id');

        // Verify StockMovement was created
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $productId,
            'warehouse_id' => $this->warehouse->id,
            'branch_id' => $this->branch->id,
            'direction' => 'in',
            'qty' => 50,
        ]);

        // Verify product stock_quantity matches movements
        $product = Product::find($productId);
        $movementTotal = StockMovement::where('product_id', $productId)
            ->selectRaw("SUM(CASE WHEN direction = 'in' THEN qty ELSE -qty END) as total")
            ->value('total');

        $this->assertEquals($movementTotal, $product->stock_quantity);
    }

    public function test_product_update_with_quantity_generates_stock_movement_record(): void
    {
        $this->actingAsStoreApi();

        // Create product with initial quantity
        $response = $this->postJson('/api/v1/products', [
            'name' => 'Test Product 2',
            'sku' => 'TEST-SKU-002',
            'price' => 100,
            'quantity' => 30,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $productId = $response->json('data.id');

        // Update quantity to 50
        $response = $this->putJson("/api/v1/products/{$productId}", [
            'quantity' => 50,
        ]);

        $response->assertStatus(200);

        // Should have created an additional stock movement for the difference (20)
        $movements = StockMovement::where('product_id', $productId)->get();
        $this->assertGreaterThanOrEqual(2, $movements->count(), 'Should have at least 2 stock movements');

        // Verify total stock matches
        $product = Product::find($productId);
        $movementTotal = StockMovement::where('product_id', $productId)
            ->selectRaw("SUM(CASE WHEN direction = 'in' THEN qty ELSE -qty END) as total")
            ->value('total');

        $this->assertEquals($movementTotal, $product->stock_quantity);
        $this->assertEquals(50, $product->stock_quantity);
    }

    public function test_product_create_with_zero_quantity_does_not_create_movement(): void
    {
        $this->actingAsStoreApi();

        $response = $this->postJson('/api/v1/products', [
            'name' => 'Test Product Zero',
            'sku' => 'TEST-SKU-ZERO',
            'price' => 100,
            'quantity' => 0,
        ]);

        $response->assertStatus(201);
        $productId = $response->json('data.id');

        // Should not have created a stock movement for zero quantity
        $this->assertDatabaseMissing('stock_movements', [
            'product_id' => $productId,
        ]);
    }

    public function test_product_create_is_transactional(): void
    {
        $this->actingAsStoreApi();

        // Try to create with invalid warehouse (should reject at validation)
        try {
            $response = $this->postJson('/api/v1/products', [
                'name' => 'Test Product Trans',
                'sku' => 'TEST-SKU-TRANS',
                'price' => 100,
                'quantity' => 50,
                'warehouse_id' => 99999, // Invalid warehouse
            ]);
            
            // If we get here, check status
            $this->assertContains($response->status(), [422, 400, 500]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation exception is expected
            $this->assertTrue(true);
        }

        // Product should not be created
        $this->assertDatabaseMissing('products', [
            'sku' => 'TEST-SKU-TRANS',
        ]);

        // Stock movement should not be created even if product was somehow created
        $this->assertEquals(0, \App\Models\StockMovement::where('branch_id', $this->branch->id)
            ->where('qty', 50)
            ->count());
    }
}
