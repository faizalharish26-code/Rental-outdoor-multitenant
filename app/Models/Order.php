<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concers\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'order_number',
        'total_amount',
        'status',
        'start_date',
        'end_date',
    ];
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(tenant::class);
    }
}
