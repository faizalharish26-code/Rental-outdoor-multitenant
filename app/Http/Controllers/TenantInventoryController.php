<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveInventoryUnitRequest;
use App\Models\InventoryUnit;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenantInventoryController extends Controller
{
    public function index(Request $request, Tenant $tenant): View
    {
        Gate::authorize('viewAny', [InventoryUnit::class, $tenant]);

        $query = $tenant->inventoryUnits()
            ->with('product')
            ->orderBy('unit_code');

        if ($request->query('status') === 'maintenance') {
            $query->where('status', 'maintenance');
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('unit_code', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search): void {
                        $productQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return view('tenant.inventory.index', [
            'tenant' => $tenant,
            'units' => $query->paginate(15)->withQueryString(),
            'maintenanceOnly' => $request->query('status') === 'maintenance',
        ]);
    }

    public function create(Tenant $tenant): View
    {
        Gate::authorize('create', [InventoryUnit::class, $tenant]);

        return view('tenant.inventory.form', [
            'tenant' => $tenant,
            'unit' => new InventoryUnit,
            'products' => $tenant->products()->orderBy('name')->get(),
        ]);
    }

    public function store(
        SaveInventoryUnitRequest $request,
        Tenant $tenant
    ): RedirectResponse {
        $data = $request->validated();

        if ($data['status'] !== 'maintenance') {
            $data['maintenance_note'] = null;
            $data['maintenance_started_at'] = null;
        }

        $tenant->inventoryUnits()->create($data);

        return to_route('tenant.inventory.index', $tenant)
            ->with('success', 'Unit inventaris berhasil ditambahkan.');
    }

    public function edit(Tenant $tenant, InventoryUnit $inventoryUnit): View
    {
        abort_unless(
            (int) $inventoryUnit->tenant_id === (int) $tenant->id,
            404
        );

        Gate::authorize('update', $inventoryUnit);

        return view('tenant.inventory.form', [
            'tenant' => $tenant,
            'unit' => $inventoryUnit,
            'products' => $tenant->products()->orderBy('name')->get(),
        ]);
    }

    public function update(
        SaveInventoryUnitRequest $request,
        Tenant $tenant,
        InventoryUnit $inventoryUnit
    ): RedirectResponse {
        abort_unless(
            (int) $inventoryUnit->tenant_id === (int) $tenant->id,
            404
        );

        Gate::authorize('update', $inventoryUnit);

        $data = $request->validated();

        if ($data['status'] !== 'maintenance') {
            $data['maintenance_note'] = null;
            $data['maintenance_started_at'] = null;
        }

        $inventoryUnit->update($data);

        return to_route('tenant.inventory.index', $tenant)
            ->with('success', 'Unit inventaris berhasil diperbarui.');
    }
}