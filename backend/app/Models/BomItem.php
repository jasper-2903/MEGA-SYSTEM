<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bom_id',
        'material_id',
        'qty_per',
        'scrap_factor',
        'notes',
    ];

    protected $casts = [
        'qty_per' => 'decimal:4',
        'scrap_factor' => 'decimal:4',
    ];

    // Relationships
    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Attributes
    public function getTotalQtyAttribute()
    {
        return $this->qty_per * (1 + $this->scrap_factor);
    }

    public function getLineCostAttribute()
    {
        return $this->total_qty * $this->material->standard_cost;
    }
}
