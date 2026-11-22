<?php

namespace App\Http\Requests;

use App\Models\InventoryUnit;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveInventoryUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');

        if (! $tenant instanceof Tenant) {
            return false;
        }

        $inventoryUnit = $this->route('inventoryUnit');

        if ($inventoryUnit instanceof InventoryUnit) {
            if ((int) $inventoryUnit->tenant_id !== (int) $tenant->id) {
                return false;
            }

            return $this->user()?->can('update', $inventoryUnit) === true;
        }

        return $this->user()?->can('create', [InventoryUnit::class, $tenant]) === true;
    }

    public function rules(): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->route('tenant');

        $inventoryUnit = $this->route('inventoryUnit');
        $ignoredId = $inventoryUnit instanceof InventoryUnit
            ? $inventoryUnit->id
            : null;

        return [
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'unit_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('inventory_units', 'unit_code')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($ignoredId, 'id'),
            ],
            'condition' => [
                'required',
                Rule::in(['good', 'fair', 'damaged']),
            ],
            'status' => [
                'required',
                Rule::in(['available', 'maintenance']),
            ],
            'maintenance_note' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:status,maintenance',
            ],
            'maintenance_started_at' => [
                'nullable',
                'date',
                'required_if:status,maintenance',
            ],
        ];
    }
}