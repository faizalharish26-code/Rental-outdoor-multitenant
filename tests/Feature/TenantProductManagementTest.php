<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_product_for_own_tenant(): void
    {
        [$owner, $tenant, $category] = $this->ownerTenantAndCategory();

        $response = $this->actingAs($owner)->post(
            route('tenant.products.store', $tenant),
            $this->validProduct($category),
        );

        $response->assertRedirect(route('tenant.products.index', $tenant));

        $this->assertDatabaseHas('products', [
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Tenda Kapasitas 4',
            'slug' => 'tenda-kapasitas-4',
        ]);
    }

    public function test_owner_cannot_open_or_update_product_from_another_tenant(): void
    {
        [$ownerA, $tenantA] = $this->ownerTenantAndCategory();
        [, $tenantB, $categoryB] = $this->ownerTenantAndCategory();

        $productB = Product::factory()->create([
            'tenant_id' => $tenantB->id,
            'category_id' => $categoryB->id,
        ]);

        $this->actingAs($ownerA)
            ->get(route('tenant.products.edit', [$tenantA, $productB]))
            ->assertNotFound();

        $this->actingAs($ownerA)
            ->put(
                route('tenant.products.update', [$tenantA, $productB]),
                $this->validProduct($categoryB),
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('products', [
            'id' => $productB->id,
            'name' => 'Produk diubah lintas tenant',
        ]);
    }

    public function test_category_from_another_tenant_is_rejected(): void
    {
        [$ownerA, $tenantA] = $this->ownerTenantAndCategory();
        [, , $categoryB] = $this->ownerTenantAndCategory();

        $this->actingAs($ownerA)
            ->post(
                route('tenant.products.store', $tenantA),
                $this->validProduct($categoryB),
            )
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseMissing('products', [
            'tenant_id' => $tenantA->id,
            'slug' => 'tenda-kapasitas-4',
        ]);
    }

    public function test_client_cannot_choose_product_tenant_id(): void
    {
        [$ownerA, $tenantA, $categoryA] = $this->ownerTenantAndCategory();
        [, $tenantB] = $this->ownerTenantAndCategory();

        $payload = $this->validProduct($categoryA);
        $payload['tenant_id'] = $tenantB->id;

        $this->actingAs($ownerA)
            ->post(route('tenant.products.store', $tenantA), $payload)
            ->assertRedirect(route('tenant.products.index', $tenantA));

        $this->assertDatabaseHas('products', [
            'tenant_id' => $tenantA->id,
            'slug' => 'tenda-kapasitas-4',
        ]);

        $this->assertDatabaseMissing('products', [
            'tenant_id' => $tenantB->id,
            'slug' => 'tenda-kapasitas-4',
        ]);
    }

    public function test_owner_can_hide_product_from_customer_catalog(): void
    {
        [$owner, $tenant, $category] = $this->ownerTenantAndCategory();

        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'is_available' => true,
        ]);

        $payload = $this->validProduct($category);
        $payload['is_available'] = '0';

        $this->actingAs($owner)
            ->put(route('tenant.products.update', [$tenant, $product]), $payload)
            ->assertRedirect(route('tenant.products.index', $tenant));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_available' => false,
        ]);
    }

    public function test_deleting_product_uses_soft_delete(): void
    {
        [$owner, $tenant, $category] = $this->ownerTenantAndCategory();

        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($owner)
            ->delete(route('tenant.products.destroy', [$tenant, $product]))
            ->assertRedirect(route('tenant.products.index', $tenant));

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    private function ownerTenantAndCategory(): array
    {
        $owner = User::factory()->create();

        $tenant = Tenant::factory()->create([
            'status' => 'active',
        ]);

        TenantStaff::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $owner->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        return [$owner, $tenant, $category];
    }

    private function validProduct(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'name' => 'Tenda Kapasitas 4',
            'slug' => 'tenda-kapasitas-4',
            'description' => 'Tenda untuk empat orang.',
            'rental_price_per_day' => '75000.00',
            'replacement_cost' => '1800000.00',
            'deposit_fee' => '100000.00',
            'is_available' => '1',
        ];
    }
}
