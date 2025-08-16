<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $reportType;
    protected $userId;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct($reportType, $userId = null, $filters = [])
    {
        $this->reportType = $reportType;
        $this->userId = $userId;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        try {
            Log::info('Starting email report job', [
                'report_type' => $this->reportType,
                'user_id' => $this->userId,
            ]);

            $user = $this->userId ? User::find($this->userId) : null;
            
            // Generate report
            $reportData = $this->generateReport($reportService);
            
            // Send email
            if ($user) {
                $this->sendReportEmail($user, $reportData);
            } else {
                $this->sendReportToAllUsers($reportData);
            }
            
            Log::info('Email report job completed successfully');
            
        } catch (\Exception $e) {
            Log::error('Email report job failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateReport($reportService)
    {
        switch ($this->reportType) {
            case 'inventory':
                return $reportService->generateInventoryReport($this->filters);
            case 'production':
                return $reportService->generateProductionReport($this->filters);
            case 'sales':
                return $reportService->generateSalesReport($this->filters);
            case 'forecast':
                return $reportService->generateForecastReport($this->filters);
            default:
                throw new \Exception('Unknown report type: ' . $this->reportType);
        }
    }

    private function sendReportEmail($user, $reportData)
    {
        // This would typically use a Mailable class
        // For now, we'll just log the action
        Log::info('Sending report email to user', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'report_type' => $this->reportType,
        ]);
    }

    private function sendReportToAllUsers($reportData)
    {
        $users = User::where('is_active', true)->get();
        
        foreach ($users as $user) {
            $this->sendReportEmail($user, $reportData);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email report job failed permanently: ' . $exception->getMessage());
    }
}