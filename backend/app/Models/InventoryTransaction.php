<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'item_type',
        'warehouse_id',
        'location_id',
        'txn_type',
        'qty',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeByItemType($query, $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeByTxnType($query, $txnType)
    {
        return $query->where('txn_type', $txnType);
    }

    public function scopeByReference($query, $referenceType, $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getFormattedQtyAttribute()
    {
        return $this->txn_type === 'adjust-' || $this->txn_type === 'issue' || $this->txn_type === 'consume'
            ? '-' . $this->qty
            : '+' . $this->qty;
    }

    public function getFormattedTxnTypeAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->txn_type));
    }
}