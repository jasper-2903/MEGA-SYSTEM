<?php

namespace App\Jobs;

use App\Services\MrpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunMrpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MrpService $mrpService): void
    {
        try {
            Log::info('Starting MRP job execution');
            
            $result = $mrpService->runMrp();
            
            Log::info('MRP job completed successfully', $result);
            
        } catch (\Exception $e) {
            Log::error('MRP job failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('MRP job failed permanently: ' . $exception->getMessage());
    }
}