<?php

namespace App\Policies;

use App\Models\InventoryUnit;
use App\Models\Tenant;
use App\Models\User;

class InventoryUnitPolicy
{
    public function viewAny(User $user, Tenant $tenant): bool
    {
        return $this->canManageInventory($user, $tenant);
    }

    public function create(User $user, Tenant $tenant): bool
    {
        return $this->canManageInventory($user, $tenant);
    }

    public function update(User $user, InventoryUnit $inventoryUnit): bool
    {
        return $user->tenantStaff()
            ->where('tenant_id', $inventoryUnit->tenant_id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }

    private function canManageInventory(User $user, Tenant $tenant): bool
    {
        return $user->tenantStaff()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}