<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'unit_of_measure',
        'standard_cost',
        'safety_stock',
        'supplier_id',
        'is_active',
    ];

    protected $casts = [
        'standard_cost' => 'decimal:2',
        'safety_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function purchaseOrderLines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'sku', 'sku')->where('item_type', 'material');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'sku', 'sku')->where('item_type', 'material');
    }

    public function reorderPolicy()
    {
        return $this->hasOne(ReorderPolicy::class, 'sku', 'sku')->where('item_type', 'material');
    }

    public function forecasts()
    {
        return $this->hasMany(Forecast::class, 'sku', 'sku')->where('item_type', 'material');
    }

    public function consumptionHistory()
    {
        return $this->hasMany(ConsumptionHistory::class, 'sku', 'sku')->where('item_type', 'material');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function getTotalOnHandAttribute()
    {
        return $this->inventory()->sum('on_hand');
    }

    public function getTotalAllocatedAttribute()
    {
        return $this->inventory()->sum('allocated');
    }

    public function getTotalOnOrderAttribute()
    {
        return $this->inventory()->sum('on_order');
    }

    public function getAvailableQtyAttribute()
    {
        return $this->total_on_hand - $this->total_allocated;
    }

    public function isBelowReorderPoint()
    {
        $policy = $this->reorderPolicy;
        if (!$policy) {
            return false;
        }

        return $this->available_qty <= $policy->reorder_point;
    }
}