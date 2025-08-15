<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'address',
        'contact_person',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function getTotalInventoryValueAttribute()
    {
        return $this->inventory()
            ->join('materials', 'inventory.sku', '=', 'materials.sku')
            ->where('inventory.item_type', 'material')
            ->sum(\DB::raw('inventory.on_hand * materials.standard_cost'));
    }
}