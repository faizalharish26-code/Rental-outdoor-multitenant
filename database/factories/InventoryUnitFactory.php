<?php

namespace Database\Factories;

use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InventoryUnit>
 */
class InventoryUnitFactory extends Factory
{
    protected $model = InventoryUnit::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        return [
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'unit_code' => 'UNIT-'.Str::upper(Str::random(8)),
            'condition' => 'good',
            'status' => 'available',
            'maintenance_note' => null,
            'maintenance_started_at' => null,
        ];
    }
}