<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'asset',
        'side',
        'order_type',
        'quantity',
        'price',
        'total',
        'status',
        'notes',
        'placed_at',
        'type',
        'target_price',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'target_price' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
