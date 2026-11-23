<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use BelongsToTenant;
    use HasFactory;
    // Hapus trait 'HasUuids' karena kita menggunakan BIGINT (auto-increment)

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'order_number',
        'total_amount',
        'deposit_amount',
        'payment_status',
        'status',
        'start_date',
        'end_date',
        'total_amount',
        'deposit_amount',
        'shipping_method',
        'shipping_address',
        'shipping_cost',
        'status',
        'payment_status',
        'tripay_reference',
        'idempotency_key',
        'cancelled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Check if the order is payable.
     */
    public function isPayable(): bool
    {
        return $this->status === 'pending'
            && in_array($this->payment_status, ['belum_bayar', 'menunggu_dp'], true);
    }
}