<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'weight_in_grams',
        'margin',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight_in_grams' => 'decimal:4',
        'margin' => 'decimal:2',
    ];
}
