<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user, Tenant $tenant): bool
    {
        return $this->canManageCatalog($user, $tenant);
    }

    public function create(User $user, Tenant $tenant): bool
    {
        return $this->canManageCatalog($user, $tenant);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->tenantStaff()
            ->where('tenant_id', $product->tenant_id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }

    private function canManageCatalog(User $user, Tenant $tenant): bool
    {
        return $user->tenantStaff()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
