<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name',
        'status',
        'started_at',
        'finished_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJobName($query, $jobName)
    {
        return $query->where('job_name', $jobName);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    public function getFormattedDurationAttribute()
    {
        $duration = $this->duration;
        if (!$duration) {
            return 'N/A';
        }

        if ($duration < 60) {
            return $duration . 's';
        } elseif ($duration < 3600) {
            return round($duration / 60, 1) . 'm';
        } else {
            return round($duration / 3600, 1) . 'h';
        }
    }

    public function isRunning()
    {
        return $this->status === 'running';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}