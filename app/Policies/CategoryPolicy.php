<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user, Tenant $tenant): bool
    {
        return $this->canManageCategories($user, $tenant);
    }

    public function create(User $user, Tenant $tenant): bool
    {
        return $this->canManageCategories($user, $tenant);
    }

    public function update(User $user, Category $category): bool
    {
        return $this->canManageCategories($user, $category->tenant);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->canManageCategories($user, $category->tenant);
    }

    private function canManageCategories(User $user, Tenant $tenant): bool
    {
        return $user->tenantStaff()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
