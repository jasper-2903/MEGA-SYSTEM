<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

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
        'on_hand' => 'integer',
        'allocated' => 'integer',
        'on_order' => 'integer',
        'last_counted_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'sku', 'sku')
                    ->where('warehouse_id', $this->warehouse_id)
                    ->where('location_id', $this->location_id);
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeByItemType($query, $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function getAvailableQtyAttribute()
    {
        return $this->on_hand - $this->allocated;
    }

    public function getTotalQtyAttribute()
    {
        return $this->on_hand + $this->on_order;
    }

    public function isLowStock()
    {
        // Check if available quantity is less than 10% of on_hand or less than 5 units
        return $this->available_qty < max(5, $this->on_hand * 0.1);
    }

    public function updateOnHand($qty, $type = 'adjust+')
    {
        if ($type === 'adjust+') {
            $this->on_hand += $qty;
        } elseif ($type === 'adjust-') {
            $this->on_hand -= $qty;
        } elseif ($type === 'receipt') {
            $this->on_hand += $qty;
            $this->on_order -= $qty;
        } elseif ($type === 'issue') {
            $this->on_hand -= $qty;
        } elseif ($type === 'consume') {
            $this->on_hand -= $qty;
        } elseif ($type === 'produce') {
            $this->on_hand += $qty;
        }

        $this->save();
    }

    public function updateAllocated($qty, $type = 'allocate')
    {
        if ($type === 'allocate') {
            $this->allocated += $qty;
        } elseif ($type === 'deallocate') {
            $this->allocated -= $qty;
        }

        $this->save();
    }

    public function updateOnOrder($qty, $type = 'order')
    {
        if ($type === 'order') {
            $this->on_order += $qty;
        } elseif ($type === 'receive') {
            $this->on_order -= $qty;
        }

        $this->save();
    }
}