<?php

namespace Tests\Feature;

use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOrderReturnInspectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_complete_an_order_and_send_a_damaged_unit_to_maintenance(): void
    {
        [$owner, $tenant, $order, $item, $unit] = $this->fulfilledOrder();

        $this->actingAs($owner)
            ->get(route('tenant.orders.show', [$tenant, $order]))
            ->assertOk()
            ->assertSee('Pemeriksaan barang kembali')
            ->assertSee("returns[{$item->id}][condition]", false);

        $response = $this->actingAs($owner)->post(
            route('tenant.orders.status', [$tenant, $order]),
            [
                'status' => 'completed',
                'returns' => [
                    $item->id => [
                        'condition' => 'damaged',
                        'note' => 'Resleting tenda terlepas.',
                    ],
                ],
            ],
        );

        $response->assertRedirect(route('tenant.orders.show', [$tenant, $order]));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('inventory_units', [
            'id' => $unit->id,
            'condition' => 'damaged',
            'status' => 'maintenance',
            'maintenance_note' => 'Resleting tenda terlepas.',
        ]);
    }

    public function test_damaged_return_requires_a_note_and_keeps_order_uncompleted(): void
    {
        [$owner, $tenant, $order, $item, $unit] = $this->fulfilledOrder();

        $this->actingAs($owner)
            ->post(route('tenant.orders.status', [$tenant, $order]), [
                'status' => 'completed',
                'returns' => [
                    $item->id => [
                        'condition' => 'damaged',
                        'note' => '',
                    ],
                ],
            ])
            ->assertSessionHasErrors("returns.{$item->id}.note");

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'fulfilled',
        ]);
        $this->assertDatabaseHas('inventory_units', [
            'id' => $unit->id,
            'condition' => 'good',
            'status' => 'available',
            'maintenance_note' => null,
        ]);
    }

    public function test_good_or_fair_return_releases_the_unit(): void
    {
        [$owner, $tenant, $order, $item, $unit] = $this->fulfilledOrder();

        $this->actingAs($owner)
            ->post(route('tenant.orders.status', [$tenant, $order]), [
                'status' => 'completed',
                'returns' => [
                    $item->id => [
                        'condition' => 'fair',
                        'note' => '',
                    ],
                ],
            ])
            ->assertRedirect(route('tenant.orders.show', [$tenant, $order]));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('inventory_units', [
            'id' => $unit->id,
            'condition' => 'fair',
            'status' => 'available',
            'maintenance_note' => null,
            'maintenance_started_at' => null,
        ]);
    }

    private function fulfilledOrder(): array
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        TenantStaff::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $owner->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $product = Product::factory()->create(['tenant_id' => $tenant->id]);
        $unit = InventoryUnit::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'unit_code' => 'TENT-RETURN-001',
            'condition' => 'good',
            'status' => 'available',
        ]);
        $order = Order::create([
            'user_id' => $owner->id,
            'tenant_id' => $tenant->id,
            'order_number' => 'ORD-RETURN-001',
            'total_amount' => 75000,
            'status' => 'fulfilled',
            'start_date' => now()->subDay(),
            'end_date' => now(),
        ]);
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'inventory_unit_id' => $unit->id,
            'quantity' => 1,
            'price_per_day' => 75000,
            'subtotal' => 75000,
        ]);

        return [$owner, $tenant, $order, $item, $unit];
    }
}
