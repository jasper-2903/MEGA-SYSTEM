<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'description',
    ];

    protected $casts = [
        'standard_cost' => 'decimal:2',
        'safety_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
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
        return $this->hasMany(Inventory::class, 'sku', 'sku')
            ->where('item_type', 'material');
    }

    public function reorderPolicy()
    {
        return $this->hasOne(ReorderPolicy::class, 'sku', 'sku')
            ->where('item_type', 'material');
    }

    public function forecasts()
    {
        return $this->hasMany(Forecast::class, 'sku', 'sku')
            ->where('item_type', 'material');
    }

    public function consumptionHistory()
    {
        return $this->hasMany(ConsumptionHistory::class, 'sku', 'sku')
            ->where('item_type', 'material');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    // Attributes
    public function getFormattedCostAttribute()
    {
        return '$' . number_format($this->standard_cost, 2);
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventory()->sum('on_hand');
    }
}
