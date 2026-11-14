<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
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

    public function staff(): HasMany
    {
        return $this->hasMany(TenantStaff::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}