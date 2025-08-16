<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'sku',
        'item_type',
        'warehouse_id',
        'location_id',
        'on_hand',
        'allocated',
        'on_order',
        'last_counted_at',
    ];

    protected $casts = [
        'on_hand' => 'decimal:4',
        'allocated' => 'decimal:4',
        'on_order' => 'decimal:4',
        'last_counted_at' => 'datetime',
    ];
}
