<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'dimensions',
        'finish',
        'unit_of_measure',
        'unit_weight',
        'price',
        'lead_time_days',
        'description',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'unit_weight' => 'decimal:2',
        'price' => 'decimal:2',
        'lead_time_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function activeBom()
    {
        return $this->hasOne(Bom::class)->where('is_active', true)->whereNull('effective_to');
    }

    public function salesOrderLines()
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function routings()
    {
        return $this->hasMany(Routing::class);
    }

    public function activeRouting()
    {
        return $this->hasOne(Routing::class)->where('is_active', true);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'sku', 'sku')->where('item_type', 'product');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'sku', 'sku')->where('item_type', 'product');
    }

    public function reorderPolicy()
    {
        return $this->hasOne(ReorderPolicy::class, 'sku', 'sku')->where('item_type', 'product');
    }

    public function forecasts()
    {
        return $this->hasMany(Forecast::class, 'sku', 'sku')->where('item_type', 'product');
    }

    public function consumptionHistory()
    {
        return $this->hasMany(ConsumptionHistory::class, 'sku', 'sku')->where('item_type', 'product');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
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

    public function getFormattedDimensionsAttribute()
    {
        if (!$this->dimensions) {
            return 'N/A';
        }

        return $this->dimensions . ' cm';
    }

    public function getFormattedWeightAttribute()
    {
        if (!$this->unit_weight) {
            return 'N/A';
        }

        return $this->unit_weight . ' kg';
    }
}