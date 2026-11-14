<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantStaff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantStaff>
 */
class TenantStaffFactory extends Factory
{
    protected $model = TenantStaff::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'role' => 'owner',
            'is_active' => true,
        ];
    }
}
