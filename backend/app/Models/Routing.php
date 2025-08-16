<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Routing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'revision',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function steps()
    {
        return $this->hasMany(RoutingStep::class)->orderBy('seq');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRevision($query, $revision)
    {
        return $query->where('revision', $revision);
    }

    public function getTotalTimeAttribute()
    {
        return $this->steps()->sum('std_time_minutes');
    }

    public function getTotalMoveTimeAttribute()
    {
        return $this->steps()->sum('move_time');
    }

    public function getTotalWaitTimeAttribute()
    {
        return $this->steps()->sum('wait_time');
    }
}