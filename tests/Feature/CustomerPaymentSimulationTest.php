<?php

namespace Tests\Feature;

use App\Livewire\CheckoutForm;
use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerPaymentSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_dp_amount_and_simulate_payment_once(): void
    {
        [$customer, $order, $payment] = $this->pendingOrder();

        $this->actingAs($customer)
            ->get(route('payments.show', $order))
            ->assertOk()
            ->assertSee('DP 50% yang harus dibayar')
            ->assertSee('Rp 50.000')
            ->assertSee('SIM-'.$order->order_number);

        $route = route('payments.simulate', $order);

        $this->actingAs($customer)->post($route)->assertRedirect(route('payments.show', $order));
        $this->actingAs($customer)->post($route)->assertRedirect(route('payments.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'deposit_amount' => 50000,
        ]);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
        $this->assertSame(1, Payment::query()->where('order_id', $order->id)->count());
    }

    public function test_customer_cannot_view_or_pay_another_customers_order(): void
    {
        [, $order] = $this->pendingOrder();
        $otherCustomer = User::factory()->create();

        $this->actingAs($otherCustomer)
            ->get(route('payments.show', $order))
            ->assertNotFound();

        $this->actingAs($otherCustomer)
            ->post(route('payments.simulate', $order))
            ->assertNotFound();

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_tenant_operator_cannot_mark_an_unpaid_order_as_paid(): void
    {
        [$customer, $order] = $this->pendingOrder();
        $tenant = $order->tenant;
        TenantStaff::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $customer->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->actingAs($customer)
            ->post(route('tenant.orders.status', [$tenant, $order]), ['status' => 'paid'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_checkout_creates_a_pending_payment_for_half_of_the_total(): void
    {
        $customer = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'rental_price_per_day' => 80000,
        ]);
        InventoryUnit::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'unit_code' => 'PAY-UNIT-001',
            'condition' => 'good',
            'status' => 'available',
        ]);

        Livewire::actingAs($customer)
            ->test(CheckoutForm::class)
            ->set('tenant_id', $tenant->id)
            ->set('start_date', now()->addDay()->format('Y-m-d\\TH:i'))
            ->set('end_date', now()->addDays(2)->format('Y-m-d\\TH:i'))
            ->set('items', [['product_id' => $product->id, 'quantity' => 1]])
            ->call('submitOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_amount' => 80000,
            'deposit_amount' => 40000,
            'payment_status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'provider' => 'simulation',
            'amount' => 40000,
            'status' => 'pending',
        ]);
    }

    private function pendingOrder(): array
    {
        $customer = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $order = Order::create([
            'user_id' => $customer->id,
            'tenant_id' => $tenant->id,
            'order_number' => 'ORD-PAY-'.strtoupper(fake()->unique()->bothify('??????')),
            'total_amount' => 100000,
            'deposit_amount' => 50000,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'simulation',
            'reference' => 'SIM-'.$order->order_number,
            'amount' => 50000,
            'status' => 'pending',
        ]);

        return [$customer, $order, $payment];
    }
}
