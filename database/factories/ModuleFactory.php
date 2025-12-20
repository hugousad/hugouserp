<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
            'name_ar' => null,
            'version' => '1.0.0',
            'is_core' => false,
            'is_active' => true,
            'description' => fake()->sentence(),
            'description_ar' => null,
            'icon' => 'heroicon-o-cube',
            'color' => fake()->hexColor(),
            'sort_order' => fake()->numberBetween(1, 100),
            'default_settings' => [],
            'pricing_type' => 'buy_sell',
            'has_variations' => false,
            'has_inventory' => true,
            'has_serial_numbers' => false,
            'has_expiry_dates' => false,
            'has_batch_numbers' => false,
            'is_rental' => false,
            'is_service' => false,
            'category' => 'general',
            'module_type' => 'data',
            'operation_config' => [],
            'integration_hooks' => [],
            'supports_reporting' => true,
            'supports_custom_fields' => true,
            'supports_items' => true,
        ];
    }

    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_service' => true,
            'has_inventory' => false,
        ]);
    }

    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_rental' => true,
        ]);
    }

    public function core(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_core' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
