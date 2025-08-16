<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\SalesOrder;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\Forecast;
use App\Models\Material;
use App\Models\Product;
use App\Models\WorkCenter;
use App\Models\ProductionLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function generateInventoryReport($filters = [])
    {
        $query = Inventory::with(['warehouse', 'location']);

        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['item_type'])) {
            $query->where('item_type', $filters['item_type']);
        }

        $inventory = $query->get();

        $report = [
            'generated_at' => now(),
            'filters' => $filters,
            'summary' => [
                'total_items' => $inventory->count(),
                'total_on_hand' => $inventory->sum('on_hand'),
                'total_allocated' => $inventory->sum('allocated'),
                'total_on_order' => $inventory->sum('on_order'),
                'total_value' => $this->calculateInventoryValue($inventory),
            ],
            'low_stock_items' => $inventory->filter(function ($item) {
                return $item->isLowStock();
            })->count(),
            'items' => $inventory->map(function ($item) {
                $product = $item->item_type === 'material' 
                    ? Material::where('sku', $item->sku)->first()
                    : Product::where('sku', $item->sku)->first();

                return [
                    'sku' => $item->sku,
                    'name' => $product ? $product->name : 'Unknown',
                    'item_type' => $item->item_type,
                    'warehouse' => $item->warehouse->name,
                    'location' => $item->location->name,
                    'on_hand' => $item->on_hand,
                    'allocated' => $item->allocated,
                    'on_order' => $item->on_order,
                    'available' => $item->available_qty,
                    'unit_cost' => $product ? ($product instanceof Material ? $product->standard_cost : $product->price) : 0,
                    'total_value' => $item->on_hand * ($product ? ($product instanceof Material ? $product->standard_cost : $product->price) : 0),
                    'is_low_stock' => $item->isLowStock(),
                ];
            }),
        ];

        return $report;
    }

    public function generateProductionReport($filters = [])
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $productionOrders = ProductionOrder::whereBetween('start_date', [$startDate, $endDate])
            ->with(['product', 'logs.routingStep.workCenter'])
            ->get();

        $workCenters = WorkCenter::with(['routingSteps.productionLogs' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        $report = [
            'generated_at' => now(),
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_orders' => $productionOrders->count(),
                'completed_orders' => $productionOrders->where('status', 'completed')->count(),
                'in_progress_orders' => $productionOrders->whereIn('status', ['released', 'in_process'])->count(),
                'total_planned_qty' => $productionOrders->sum('qty_planned'),
                'total_completed_qty' => $productionOrders->sum('qty_completed'),
                'completion_rate' => $productionOrders->sum('qty_planned') > 0 
                    ? round(($productionOrders->sum('qty_completed') / $productionOrders->sum('qty_planned')) * 100, 2)
                    : 0,
            ],
            'work_center_utilization' => $workCenters->map(function ($workCenter) use ($startDate, $endDate) {
                $totalCapacity = $workCenter->capacity_per_day * $startDate->diffInDays($endDate);
                $totalUsed = $workCenter->productionLogs->sum(function ($log) {
                    return $log->qty_completed * $log->routingStep->std_time_minutes;
                });

                return [
                    'work_center' => $workCenter->name,
                    'total_capacity' => $totalCapacity,
                    'total_used' => $totalUsed,
                    'utilization_percentage' => $totalCapacity > 0 ? round(($totalUsed / $totalCapacity) * 100, 2) : 0,
                    'is_overloaded' => $totalUsed > $totalCapacity,
                ];
            }),
            'orders' => $productionOrders->map(function ($order) {
                return [
                    'wo_number' => $order->wo_number,
                    'product' => $order->product->name,
                    'status' => $order->status,
                    'qty_planned' => $order->qty_planned,
                    'qty_completed' => $order->qty_completed,
                    'completion_percentage' => $order->completion_percentage,
                    'start_date' => $order->start_date,
                    'due_date' => $order->due_date,
                    'is_overdue' => $order->is_overdue,
                    'priority' => $order->priority_label,
                ];
            }),
        ];

        return $report;
    }

    public function generateSalesReport($filters = [])
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $salesOrders = SalesOrder::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'lines.product'])
            ->get();

        $report = [
            'generated_at' => now(),
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_orders' => $salesOrders->count(),
                'total_revenue' => $salesOrders->sum('total'),
                'average_order_value' => $salesOrders->count() > 0 ? round($salesOrders->sum('total') / $salesOrders->count(), 2) : 0,
                'shipped_orders' => $salesOrders->where('status', 'shipped')->count(),
                'pending_orders' => $salesOrders->whereIn('status', ['confirmed', 'planned', 'in_production'])->count(),
                'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($salesOrders),
            ],
            'top_products' => $this->getTopProducts($salesOrders),
            'top_customers' => $this->getTopCustomers($salesOrders),
            'orders' => $salesOrders->map(function ($order) {
                return [
                    'so_number' => $order->so_number,
                    'customer' => $order->customer->name,
                    'status' => $order->status,
                    'total' => $order->total,
                    'requested_date' => $order->requested_date,
                    'promised_date' => $order->promised_date,
                    'is_overdue' => $order->is_overdue,
                    'completion_percentage' => $order->completion_percentage,
                ];
            }),
        ];

        return $report;
    }

    public function generateForecastReport($filters = [])
    {
        $forecasts = Forecast::where('is_active', true)
            ->with(['material', 'product'])
            ->get();

        $report = [
            'generated_at' => now(),
            'summary' => [
                'total_forecasts' => $forecasts->count(),
                'high_confidence' => $forecasts->where('confidence_level', 'High')->count(),
                'medium_confidence' => $forecasts->where('confidence_level', 'Medium')->count(),
                'low_confidence' => $forecasts->where('confidence_level', 'Low')->count(),
                'total_forecast_qty' => $forecasts->sum('forecast_qty'),
            ],
            'by_method' => [
                'SMA3' => $forecasts->where('method', 'SMA3')->count(),
                'SMA6' => $forecasts->where('method', 'SMA6')->count(),
                'consumption_rate' => $forecasts->where('method', 'consumption_rate')->count(),
            ],
            'forecasts' => $forecasts->map(function ($forecast) {
                $item = $forecast->item_type === 'material' 
                    ? Material::where('sku', $forecast->sku)->first()
                    : Product::where('sku', $forecast->sku)->first();

                return [
                    'sku' => $forecast->sku,
                    'name' => $item ? $item->name : 'Unknown',
                    'item_type' => $forecast->item_type,
                    'method' => $forecast->method,
                    'forecast_qty' => $forecast->forecast_qty,
                    'confidence_level' => $forecast->confidence_level,
                    'mad' => $forecast->mad,
                    'horizon' => $forecast->formatted_horizon,
                ];
            }),
        ];

        return $report;
    }

    private function calculateInventoryValue($inventory)
    {
        $totalValue = 0;

        foreach ($inventory as $item) {
            $product = $item->item_type === 'material' 
                ? Material::where('sku', $item->sku)->first()
                : Product::where('sku', $item->sku)->first();

            if ($product) {
                $unitCost = $product instanceof Material ? $product->standard_cost : $product->price;
                $totalValue += $item->on_hand * $unitCost;
            }
        }

        return $totalValue;
    }

    private function calculateOnTimeDeliveryRate($salesOrders)
    {
        $shippedOrders = $salesOrders->where('status', 'shipped');
        
        if ($shippedOrders->count() === 0) {
            return 0;
        }

        $onTimeDeliveries = $shippedOrders->filter(function ($order) {
            return $order->promised_date >= $order->created_at;
        })->count();

        return round(($onTimeDeliveries / $shippedOrders->count()) * 100, 2);
    }

    private function getTopProducts($salesOrders)
    {
        $productSales = [];

        foreach ($salesOrders as $order) {
            foreach ($order->lines as $line) {
                $productName = $line->product->name;
                
                if (!isset($productSales[$productName])) {
                    $productSales[$productName] = [
                        'name' => $productName,
                        'qty_sold' => 0,
                        'revenue' => 0,
                    ];
                }

                $productSales[$productName]['qty_sold'] += $line->qty;
                $productSales[$productName]['revenue'] += $line->line_total;
            }
        }

        return collect($productSales)
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
    }

    private function getTopCustomers($salesOrders)
    {
        $customerSales = [];

        foreach ($salesOrders as $order) {
            $customerName = $order->customer->name;
            
            if (!isset($customerSales[$customerName])) {
                $customerSales[$customerName] = [
                    'name' => $customerName,
                    'orders' => 0,
                    'revenue' => 0,
                ];
            }

            $customerSales[$customerName]['orders']++;
            $customerSales[$customerName]['revenue'] += $order->total;
        }

        return collect($customerSales)
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
    }
}