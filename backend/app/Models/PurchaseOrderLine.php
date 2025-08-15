<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'qty_ordered',
        'qty_received',
        'unit_cost',
        'expected_date_line',
        'status',
    ];

    protected $casts = [
        'qty_ordered' => 'integer',
        'qty_received' => 'integer',
        'unit_cost' => 'decimal:2',
        'expected_date_line' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'partial']);
    }

    public function getRemainingQtyAttribute()
    {
        return $this->qty_ordered - $this->qty_received;
    }

    public function getLineTotalAttribute()
    {
        return $this->qty_ordered * $this->unit_cost;
    }

    public function getReceivedTotalAttribute()
    {
        return $this->qty_received * $this->unit_cost;
    }

    public function isFullyReceived()
    {
        return $this->qty_received >= $this->qty_ordered;
    }

    public function isPartiallyReceived()
    {
        return $this->qty_received > 0 && $this->qty_received < $this->qty_ordered;
    }

    public function isOverdue()
    {
        return $this->expected_date_line < now() && !$this->isFullyReceived();
    }
}