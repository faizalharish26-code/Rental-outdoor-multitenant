<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_only_sees_active_tenants(): void
    {
        $activeTenant = Tenant::factory()->create(['name' => 'Merbabu Gear', 'status' => 'active']);
        $inactiveTenant = Tenant::factory()->create(['name' => 'Closed Gear', 'status' => 'inactive']);

        $response = $this->get(route('customer.tenants.index'));

        $response->assertOk();
        $response->assertSee($activeTenant->name);
        $response->assertDontSee($inactiveTenant->name);
    }

    public function test_inactive_tenant_catalog_returns_404(): void
    {
        $inactiveTenant = Tenant::factory()->create(['status' => 'inactive']);

        $this->get(route('customer.catalog.index', $inactiveTenant))
            ->assertNotFound();
    }

    public function test_catalog_only_shows_available_products_for_current_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);

        $availableProductA = Product::factory()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Tenda Kapasitas 4 Orang',
            'is_available' => true,
        ]);

        $unavailableProductA = Product::factory()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Carrier Rusak',
            'is_available' => false,
        ]);

        $productB = Product::factory()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Kompor Portable B',
            'is_available' => true,
        ]);

        $response = $this->get(route('customer.catalog.index', $tenantA));

        $response->assertOk();
        $response->assertSee($availableProductA->name);
        $response->assertDontSee($unavailableProductA->name);
        $response->assertDontSee($productB->name);
    }

    public function test_cross_tenant_category_filter_and_product_detail_return_404(): void
    {
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);

        $categoryB = Category::factory()->create(['tenant_id' => $tenantB->id]);
        $productB = Product::factory()->create([
            'tenant_id' => $tenantB->id,
            'is_available' => true,
        ]);

        // Filter kategori milik Tenant B di halaman Tenant A -> 404
        $this->get(route('customer.catalog.index', [$tenantA, 'category' => $categoryB->id]))
            ->assertNotFound();

        // Akses detail produk Tenant B melalui URL Tenant A -> 404
        $this->get(route('customer.catalog.show', [$tenantA, $productB]))
            ->assertNotFound();
    }
}