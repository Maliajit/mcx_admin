<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'margin',
        'adjustment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'margin' => 'decimal:2',
        'adjustment' => 'decimal:2',
    ];
}
