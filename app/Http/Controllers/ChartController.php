<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Sale;
use App\Models\SupplyItem;
use App\Models\Trips;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index()
    {
        return view('charts.index');
    }

    /**
     * Get stock data grouped by date
     */
    public function stockData()
    {
        $data = Stock::select(DB::raw('Date'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('Date')
            ->orderBy('Date')
            ->limit(30)
            ->get();

        return response()->json([
            'labels' => $data->pluck('Date')->toArray(),
            'data' => $data->pluck('total_quantity')->toArray(),
        ]);
    }

    /**
     * Get sales data grouped by date
     */
    public function saleData()
    {
        $data = Sale::select(DB::raw('Date'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('Date')
            ->orderBy('Date')
            ->limit(30)
            ->get();

        return response()->json([
            'labels' => $data->pluck('Date')->toArray(),
            'data' => $data->pluck('total_amount')->toArray(),
        ]);
    }

    /**
     * Get supply items cost data grouped by date
     */
    public function costData()
    {
        $data = SupplyItem::select(DB::raw('Date'), DB::raw('SUM(cost) as total_cost'))
            ->groupBy('Date')
            ->orderBy('Date')
            ->limit(30)
            ->get();

        return response()->json([
            'labels' => $data->pluck('Date')->toArray(),
            'data' => $data->pluck('total_cost')->toArray(),
        ]);
    }

    /**
     * Get vehicle trips data (km) grouped by date
     */
    public function vehicleData()
    {
        $data = Trips::select(DB::raw('Date'), DB::raw('SUM(km) as total_km'))
            ->groupBy('Date')
            ->orderBy('Date')
            ->limit(30)
            ->get();

        return response()->json([
            'labels' => $data->pluck('Date')->toArray(),
            'data' => $data->pluck('total_km')->toArray(),
        ]);
    }
}
