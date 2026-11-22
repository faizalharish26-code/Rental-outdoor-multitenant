<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'rental_price_per_day',
        'replacement_cost',
        'deposit_fee',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'rental_price_per_day' => 'decimal:2',
            'replacement_cost' => 'decimal:2',
            'deposit_fee' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<InventoryUnit, $this> */
    public function inventoryUnits(): HasMany
    {
        return $this->hasMany(InventoryUnit::class);
    }
}