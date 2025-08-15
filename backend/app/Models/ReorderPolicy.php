<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReorderPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'item_type',
        'reorder_point',
        'reorder_qty',
        'min_level',
        'max_level',
        'planning_strategy',
        'is_active',
    ];

    protected $casts = [
        'reorder_point' => 'integer',
        'reorder_qty' => 'integer',
        'min_level' => 'integer',
        'max_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeByItemType($query, $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeByStrategy($query, $strategy)
    {
        return $query->where('planning_strategy', $strategy);
    }

    public function getSuggestedReorderQtyAttribute()
    {
        if ($this->planning_strategy === 'lot-for-lot') {
            return $this->reorder_qty;
        }

        // ROP strategy - calculate based on demand
        return max($this->reorder_qty, $this->min_level);
    }
}