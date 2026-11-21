<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'address',
        'operational_hours',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'operational_hours' => 'array',
        ];
    }

    /**
     * Gunakan slug tenant sebagai kunci pada URL.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<TenantStaff, $this> */
    public function staff(): HasMany
    {
        return $this->hasMany(TenantStaff::class);
    }

    /** @return HasMany<Category, $this> */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
