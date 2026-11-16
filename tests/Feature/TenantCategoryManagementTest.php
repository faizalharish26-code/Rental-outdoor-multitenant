<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_category_pages(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Kategori Milik Sendiri',
        ]);

        $otherCategory = Category::factory()->create([
            'name' => 'Kategori Tenant Lain',
        ]);

        $this->actingAs($owner)
            ->get(route('tenant.categories.index', $tenant))
            ->assertOk()
            ->assertSeeText($category->name)
            ->assertDontSeeText($otherCategory->name);

        $this->actingAs($owner)
            ->get(route('tenant.categories.create', $tenant))
            ->assertOk();

        $this->actingAs($owner)
            ->get(route('tenant.categories.edit', [$tenant, $category]))
            ->assertOk();
    }

    public function test_owner_can_create_category_without_choosing_tenant_id(): void
    {
        [$owner, $tenant] = $this->ownerTenant();
        $otherTenant = Tenant::factory()->create();

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Alat Masak',
                'slug' => 'alat-masak',
                'tenant_id' => $otherTenant->id,
            ])
            ->assertRedirect(route('tenant.categories.index', $tenant))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'tenant_id' => $tenant->id,
            'name' => 'Alat Masak',
            'slug' => 'alat-masak',
        ]);

        $this->assertDatabaseMissing('categories', [
            'tenant_id' => $otherTenant->id,
            'slug' => 'alat-masak',
        ]);
    }

    public function test_owner_can_update_category_and_keep_its_slug(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenda',
            'slug' => 'tenda',
        ]);

        $this->actingAs($owner)
            ->put(route('tenant.categories.update', [$tenant, $category]), [
                'name' => 'Tenda Camping',
                'slug' => 'tenda',
            ])
            ->assertRedirect(route('tenant.categories.index', $tenant))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'tenant_id' => $tenant->id,
            'name' => 'Tenda Camping',
            'slug' => 'tenda',
        ]);
    }

    public function test_duplicate_slug_is_rejected_inside_same_tenant(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        Category::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'tenda',
        ]);

        $otherCategory = Category::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'tas',
        ]);

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Kategori Duplikat',
                'slug' => 'tenda',
            ])
            ->assertSessionHasErrors('slug');

        $this->actingAs($owner)
            ->put(route('tenant.categories.update', [$tenant, $otherCategory]), [
                'name' => 'Kategori Duplikat',
                'slug' => 'tenda',
            ])
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseHas('categories', [
            'id' => $otherCategory->id,
            'slug' => 'tas',
        ]);

        $this->assertDatabaseMissing('categories', [
            'tenant_id' => $tenant->id,
            'name' => 'Kategori Duplikat',
        ]);
    }

    public function test_same_slug_is_allowed_in_different_tenants(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        Category::factory()->create([
            'slug' => 'tenda',
        ]);

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Tenda',
                'slug' => 'tenda',
            ])
            ->assertRedirect(route('tenant.categories.index', $tenant));

        $this->assertDatabaseHas('categories', [
            'tenant_id' => $tenant->id,
            'slug' => 'tenda',
        ]);
    }

    public function test_invalid_category_input_is_rejected(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => '',
                'slug' => '',
            ])
            ->assertSessionHasErrors(['name', 'slug']);

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => str_repeat('a', 201),
                'slug' => 'Slug Dengan Spasi',
            ])
            ->assertSessionHasErrors(['name', 'slug']);

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_owner_cannot_edit_update_or_delete_other_tenant_category(): void
    {
        [$owner, $tenant] = $this->ownerTenant();
        $foreignCategory = Category::factory()->create();

        $this->actingAs($owner)
            ->get(route('tenant.categories.edit', [$tenant, $foreignCategory]))
            ->assertNotFound();

        $this->actingAs($owner)
            ->put(route('tenant.categories.update', [$tenant, $foreignCategory]), [
                'name' => 'Perubahan Ilegal',
                'slug' => 'perubahan-ilegal',
            ])
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('tenant.categories.destroy', [$tenant, $foreignCategory]))
            ->assertNotFound();

        $this->assertDatabaseHas('categories', [
            'id' => $foreignCategory->id,
            'name' => $foreignCategory->name,
        ]);
    }

    public function test_user_without_membership_cannot_manage_categories(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();
        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->get(route('tenant.categories.index', $tenant))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('tenant.categories.create', $tenant))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Tenda',
                'slug' => 'tenda',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('tenant.categories.edit', [$tenant, $category]))
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('tenant.categories.update', [$tenant, $category]), [
                'name' => 'Perubahan Ilegal',
                'slug' => 'perubahan-ilegal',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('tenant.categories.destroy', [$tenant, $category]))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function test_inactive_membership_cannot_create_category(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        TenantStaff::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $owner->id)
            ->update(['is_active' => false]);

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Tenda',
                'slug' => 'tenda',
            ])
            ->assertForbidden();
    }

    public function test_manager_can_create_category(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        TenantStaff::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $owner->id)
            ->update(['role' => 'manager']);

        $this->actingAs($owner)
            ->post(route('tenant.categories.store', $tenant), [
                'name' => 'Tenda',
                'slug' => 'tenda',
            ])
            ->assertRedirect(route('tenant.categories.index', $tenant));
    }

    public function test_owner_can_delete_empty_category(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($owner)
            ->delete(route('tenant.categories.destroy', [$tenant, $category]))
            ->assertRedirect(route('tenant.categories.index', $tenant))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_used_by_active_or_soft_deleted_product_cannot_be_deleted(): void
    {
        [$owner, $tenant] = $this->ownerTenant();

        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($owner)
            ->delete(route('tenant.categories.destroy', [$tenant, $category]))
            ->assertRedirect(route('tenant.categories.index', $tenant))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);

        $product->delete();

        $this->actingAs($owner)
            ->delete(route('tenant.categories.destroy', [$tenant, $category]))
            ->assertRedirect(route('tenant.categories.index', $tenant))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function ownerTenant(): array
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

        return [$owner, $tenant];
    }
}
