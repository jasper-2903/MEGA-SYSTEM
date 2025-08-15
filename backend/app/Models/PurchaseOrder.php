<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'status',
        'expected_date',
        'subtotal',
        'tax',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expected_date', [$startDate, $endDate]);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['draft', 'approved', 'sent']);
    }

    public function getIsOverdueAttribute()
    {
        return $this->expected_date < now() && in_array($this->status, ['draft', 'approved', 'sent']);
    }

    public function getReceivedQtyAttribute()
    {
        return $this->lines()->sum('qty_received');
    }

    public function getOrderedQtyAttribute()
    {
        return $this->lines()->sum('qty_ordered');
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->ordered_qty == 0) {
            return 0;
        }

        return round(($this->received_qty / $this->ordered_qty) * 100, 2);
    }

    public function isFullyReceived()
    {
        return $this->received_qty >= $this->ordered_qty;
    }
}