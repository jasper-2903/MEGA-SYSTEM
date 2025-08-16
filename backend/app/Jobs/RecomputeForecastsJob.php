<?php

namespace App\Jobs;

use App\Services\ForecastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecomputeForecastsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $methods;

    /**
     * Create a new job instance.
     */
    public function __construct($methods = ['SMA3', 'SMA6', 'consumption_rate'])
    {
        $this->methods = $methods;
    }

    /**
     * Execute the job.
     */
    public function handle(ForecastService $forecastService): void
    {
        try {
            Log::info('Starting forecast recomputation job');
            
            $result = $forecastService->recomputeForecasts($this->methods);
            
            Log::info('Forecast recomputation job completed successfully', $result);
            
        } catch (\Exception $e) {
            Log::error('Forecast recomputation job failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Forecast recomputation job failed permanently: ' . $exception->getMessage());
    }
}