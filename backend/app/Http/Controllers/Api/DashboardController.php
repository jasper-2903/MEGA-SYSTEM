<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function metrics()
    {
        return response()->json([
            'inventoryCount' => 1234,
            'ordersCount' => 87,
            'productionOutput' => 452,
            'salesTotal' => 12890.50,
        ]);
    }

    public function charts()
    {
        return response()->json([
            'inventoryTrend' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data' => [100, 120, 150, 130, 160, 180],
            ],
            'productionEfficiency' => [
                'labels' => ['Cutting', 'Assembly', 'Finishing', 'QC'],
                'data' => [85, 92, 78, 88],
            ],
            'orderDistribution' => [
                'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
                'data' => [12, 19, 7, 30, 3],
            ],
        ]);
    }
}