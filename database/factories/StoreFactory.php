<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['shopify', 'woocommerce', 'laravel', 'custom']),
            'url' => fake()->url(),
            'branch_id' => Branch::factory(),
            'is_active' => true,
            'settings' => [],
            'last_sync_at' => null,
        ];
    }

    public function shopify(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'shopify',
        ]);
    }

    public function woocommerce(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'woocommerce',
        ]);
    }

    public function laravel(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'laravel',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
