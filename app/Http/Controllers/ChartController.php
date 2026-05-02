<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Sale;
use App\Models\SupplyItem;
use App\Models\Trips;
use App\Models\Product;
use App\Models\Consumable;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get summary statistics
        $stats = [
            'total_sales' => Sale::sum('amount'),
            'total_cost' => SupplyItem::sum('cost'),
            'total_profit' => (Sale::sum('amount') ?? 0) - (SupplyItem::sum('cost') ?? 0),
            'total_vehicles_km' => Trips::sum('km'),
            'products_count' => Product::count(),
            'consumables_count' => Consumable::count(),
        ];
        
        return view('charts.index', compact('stats'));
    }

    /**
     * Get stock data grouped by date for last 90 days
     */
    public function stockData()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = Stock::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            // Fill missing dates with 0
            $result = $this->fillMissingDates($data, $days, 'Date', 'total_quantity');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total' => $data->sum('total_quantity'),
                    'average' => round($data->avg('total_quantity'), 2),
                    'max' => $data->max('total_quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed stock data by product
     */
    public function stockDataByProduct()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = Stock::where('Date', '>=', $startDate)
                ->with('product')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'labels' => $data->map(fn($item) => $item->product->name ?? 'Unknown'),
                'data' => $data->pluck('total_quantity'),
                'productIds' => $data->pluck('product_id'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get sales data grouped by date
     */
    public function saleData()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = Sale::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->fillMissingDates($data, $days, 'Date', 'total_amount');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_amount' => $data->sum('total_amount'),
                    'average_amount' => round($data->avg('total_amount'), 2),
                    'max_amount' => $data->max('total_amount'),
                    'total_quantity' => $data->sum('total_quantity'),
                    'transaction_count' => $data->sum('count'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed sales data by product
     */
    public function saleDataByProduct()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = Sale::where('Date', '>=', $startDate)
                ->with('product')
                ->select('product_id', DB::raw('SUM(amount) as total_amount'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderBy('total_amount', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'labels' => $data->map(fn($item) => $item->product->name ?? 'Unknown'),
                'data' => $data->pluck('total_amount'),
                'quantities' => $data->pluck('total_quantity'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get supply items cost data grouped by date
     */
    public function costData()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = SupplyItem::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(cost) as total_cost'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->fillMissingDates($data, $days, 'Date', 'total_cost');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_cost' => $data->sum('total_cost'),
                    'average_cost' => round($data->avg('total_cost'), 2),
                    'max_cost' => $data->max('total_cost'),
                    'total_items' => $data->sum('total_quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed cost data by consumable
     */
    public function costDataByConsumable()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = SupplyItem::where('Date', '>=', $startDate)
                ->with('consumable')
                ->select('consumable_id', DB::raw('SUM(cost) as total_cost'), DB::raw('COUNT(*) as count'))
                ->groupBy('consumable_id')
                ->orderBy('total_cost', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'labels' => $data->map(fn($item) => $item->consumable->item ?? 'Unknown'),
                'data' => $data->pluck('total_cost'),
                'count' => $data->pluck('count'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get vehicle trips data (km) grouped by date
     */
    public function vehicleData()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $data = Trips::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(km) as total_km'), DB::raw('COUNT(*) as count'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->fillMissingDates($data, $days, 'Date', 'total_km');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_km' => $data->sum('total_km'),
                    'average_km' => round($data->avg('total_km'), 2),
                    'max_km' => $data->max('total_km'),
                    'trip_count' => $data->sum('count'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get profit/loss analysis
     */
    public function profitLossData()
    {
        try {
            $days = request()->query('days', 30);
            $startDate = now()->subDays($days);
            
            $sales = Sale::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get()
                ->keyBy('Date');
                
            $costs = SupplyItem::where('Date', '>=', $startDate)
                ->select('Date', DB::raw('SUM(cost) as total_cost'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get()
                ->keyBy('Date');

            $dates = collect();
            $allDates = collect([$sales->keys(), $costs->keys()])->flatten()->unique()->sort();
            
            $profitData = [];
            $salesData = [];
            $costsData = [];
            
            foreach ($allDates as $date) {
                $dates->push($date);
                $salesAmount = $sales->get($date)?->total_amount ?? 0;
                $costAmount = $costs->get($date)?->total_cost ?? 0;
                $profitData[] = $salesAmount - $costAmount;
                $salesData[] = $salesAmount;
                $costsData[] = $costAmount;
            }
            
            return response()->json([
                'labels' => $dates->toArray(),
                'datasets' => [
                    [
                        'label' => 'Sales (Rs.)',
                        'data' => $salesData,
                        'borderColor' => 'rgb(75, 192, 192)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
                    ],
                    [
                        'label' => 'Costs (Rs.)',
                        'data' => $costsData,
                        'borderColor' => 'rgb(255, 99, 132)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
                    ],
                    [
                        'label' => 'Profit (Rs.)',
                        'data' => $profitData,
                        'borderColor' => 'rgb(76, 175, 80)',
                        'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                    ],
                ],
                'summary' => [
                    'total_sales' => array_sum($salesData),
                    'total_cost' => array_sum($costsData),
                    'total_profit' => array_sum($profitData),
                    'profit_margin' => array_sum($salesData) > 0 ? round((array_sum($profitData) / array_sum($salesData)) * 100, 2) : 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper function to fill missing dates with 0
     */
    private function fillMissingDates($data, $days, $dateColumn = 'Date', $dataColumn = 'data')
    {
        $labels = [];
        $values = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M d');
            
            $record = $data->firstWhere($dateColumn, $date);
            $values[] = $record?->$dataColumn ?? 0;
        }
        
        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }
}
