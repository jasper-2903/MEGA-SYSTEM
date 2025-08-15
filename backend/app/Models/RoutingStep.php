<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'routing_id',
        'seq',
        'work_center_id',
        'name',
        'std_time_minutes',
        'move_time',
        'wait_time',
        'notes',
    ];

    protected $casts = [
        'seq' => 'integer',
        'std_time_minutes' => 'integer',
        'move_time' => 'integer',
        'wait_time' => 'integer',
    ];

    public function routing()
    {
        return $this->belongsTo(Routing::class);
    }

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class);
    }

    public function productionLogs()
    {
        return $this->hasMany(ProductionLog::class);
    }

    public function scopeBySequence($query, $seq)
    {
        return $query->where('seq', $seq);
    }

    public function scopeByWorkCenter($query, $workCenterId)
    {
        return $query->where('work_center_id', $workCenterId);
    }

    public function getTotalTimeAttribute()
    {
        return $this->std_time_minutes + $this->move_time + $this->wait_time;
    }

    public function getFormattedTimeAttribute()
    {
        $hours = floor($this->std_time_minutes / 60);
        $minutes = $this->std_time_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }
}