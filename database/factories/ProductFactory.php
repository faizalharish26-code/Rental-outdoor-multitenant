<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => function (array $attributes): int {
                return Category::factory()
                    ->create(['tenant_id' => $attributes['tenant_id']])
                    ->id;
            },
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'rental_price_per_day' => 75000,
            'replacement_cost' => 1800000,
            'deposit_fee' => 100000,
            'is_available' => true,
        ];
    }
}