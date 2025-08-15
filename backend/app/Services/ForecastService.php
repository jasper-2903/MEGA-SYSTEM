<?php

namespace App\Services;

use App\Models\Forecast;
use App\Models\ConsumptionHistory;
use App\Models\Material;
use App\Models\Product;
use App\Models\SystemJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForecastService
{
    public function recomputeForecasts($methods = ['SMA3', 'SMA6', 'consumption_rate'])
    {
        $job = SystemJob::create([
            'job_name' => 'FORECAST_RECOMPUTE',
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            Log::info('Starting forecast recomputation');

            $forecastsCreated = 0;

            foreach ($methods as $method) {
                $forecastsCreated += $this->computeForecastByMethod($method);
            }

            $job->update([
                'status' => 'completed',
                'finished_at' => now(),
                'notes' => "Forecast recomputation completed. Created {$forecastsCreated} forecasts.",
            ]);

            Log::info('Forecast recomputation completed successfully');

            return [
                'success' => true,
                'forecasts_created' => $forecastsCreated,
                'methods' => $methods,
            ];

        } catch (\Exception $e) {
            $job->update([
                'status' => 'failed',
                'finished_at' => now(),
                'notes' => 'Forecast recomputation failed: ' . $e->getMessage(),
            ]);

            Log::error('Forecast recomputation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function computeForecastByMethod($method)
    {
        $forecastsCreated = 0;

        // Get all active materials and products
        $materials = Material::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        $items = $materials->concat($products);

        foreach ($items as $item) {
            $itemType = $item instanceof Material ? 'material' : 'product';
            $consumptionData = $this->getConsumptionData($item->sku, $itemType);

            if (empty($consumptionData)) {
                continue;
            }

            $forecastQty = $this->calculateForecast($consumptionData, $method);
            $mad = $this->calculateMAD($consumptionData, $forecastQty, $method);

            // Deactivate old forecasts for this SKU and method
            Forecast::where('sku', $item->sku)
                ->where('item_type', $itemType)
                ->where('method', $method)
                ->update(['is_active' => false]);

            // Create new forecast
            Forecast::create([
                'sku' => $item->sku,
                'item_type' => $itemType,
                'horizon_start' => now(),
                'horizon_end' => now()->addDays(30),
                'method' => $method,
                'forecast_qty' => $forecastQty,
                'mad' => $mad,
                'is_active' => true,
            ]);

            $forecastsCreated++;
        }

        return $forecastsCreated;
    }

    private function getConsumptionData($sku, $itemType)
    {
        $query = ConsumptionHistory::where('sku', $sku)
            ->where('item_type', $itemType)
            ->orderBy('date', 'desc')
            ->limit(90); // Last 90 days

        if ($itemType === 'material') {
            return $query->pluck('qty_issued')->toArray();
        } else {
            return $query->pluck('qty_sold')->toArray();
        }
    }

    private function calculateForecast($consumptionData, $method)
    {
        switch ($method) {
            case 'SMA3':
                return $this->calculateSMA($consumptionData, 3);
            case 'SMA6':
                return $this->calculateSMA($consumptionData, 6);
            case 'consumption_rate':
                return $this->calculateConsumptionRate($consumptionData);
            default:
                return 0;
        }
    }

    private function calculateSMA($data, $period)
    {
        if (count($data) < $period) {
            return 0;
        }

        $sum = array_sum(array_slice($data, 0, $period));
        return round($sum / $period);
    }

    private function calculateConsumptionRate($data)
    {
        if (empty($data)) {
            return 0;
        }

        $totalConsumption = array_sum($data);
        $days = count($data);
        $dailyRate = $totalConsumption / $days;

        // Project for 30 days
        return round($dailyRate * 30);
    }

    private function calculateMAD($actualData, $forecast, $method)
    {
        if (empty($actualData)) {
            return 0;
        }

        $errors = [];
        $period = $method === 'SMA3' ? 3 : ($method === 'SMA6' ? 6 : 1);

        for ($i = $period; $i < count($actualData); $i++) {
            if ($method === 'consumption_rate') {
                $predicted = $this->calculateConsumptionRate(array_slice($actualData, $i - 30, 30));
            } else {
                $predicted = $this->calculateSMA(array_slice($actualData, $i - $period, $period));
            }

            $actual = $actualData[$i];
            $errors[] = abs($actual - $predicted);
        }

        if (empty($errors)) {
            return 0;
        }

        return round(array_sum($errors) / count($errors), 2);
    }

    public function getForecastRecommendations()
    {
        $recommendations = [];

        $forecasts = Forecast::where('is_active', true)
            ->with(['material', 'product'])
            ->get();

        foreach ($forecasts as $forecast) {
            $item = $forecast->item_type === 'material' 
                ? Material::where('sku', $forecast->sku)->first()
                : Product::where('sku', $forecast->sku)->first();

            if (!$item) {
                continue;
            }

            $reorderPolicy = $item->reorderPolicy;
            if (!$reorderPolicy) {
                continue;
            }

            $currentInventory = $item->total_on_hand;
            $currentAllocated = $item->total_allocated;
            $availableQty = $currentInventory - $currentAllocated;

            // Calculate suggested reorder point based on forecast
            $dailyDemand = $forecast->forecast_qty / 30; // Convert monthly to daily
            $leadTime = $forecast->item_type === 'material' 
                ? ($item->supplier->lead_time_days ?? 7)
                : ($item->lead_time_days ?? 5);

            $suggestedReorderPoint = round($dailyDemand * $leadTime * 1.2); // 20% safety factor
            $suggestedReorderQty = round($dailyDemand * 30); // One month supply

            $recommendations[] = [
                'sku' => $forecast->sku,
                'item_type' => $forecast->item_type,
                'name' => $item->name,
                'current_reorder_point' => $reorderPolicy->reorder_point,
                'suggested_reorder_point' => $suggestedReorderPoint,
                'current_reorder_qty' => $reorderPolicy->reorder_qty,
                'suggested_reorder_qty' => $suggestedReorderQty,
                'current_inventory' => $currentInventory,
                'available_qty' => $availableQty,
                'forecast_qty' => $forecast->forecast_qty,
                'confidence_level' => $forecast->confidence_level,
                'method' => $forecast->method,
                'mad' => $forecast->mad,
            ];
        }

        return $recommendations;
    }

    public function applyForecastRecommendations($recommendations)
    {
        $applied = 0;

        foreach ($recommendations as $recommendation) {
            $reorderPolicy = ReorderPolicy::where('sku', $recommendation['sku'])
                ->where('item_type', $recommendation['item_type'])
                ->where('is_active', true)
                ->first();

            if ($reorderPolicy) {
                $reorderPolicy->update([
                    'reorder_point' => $recommendation['suggested_reorder_point'],
                    'reorder_qty' => $recommendation['suggested_reorder_qty'],
                ]);

                $applied++;
            }
        }

        return $applied;
    }
}