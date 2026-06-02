<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Sale;
use App\Models\SupplyItem;
use App\Models\Trips;
use App\Models\Product;
use App\Models\Consumable;
use App\Models\Logistics;
use App\Models\Turning;
use App\Models\Jcb;
use App\Models\Weight;
use App\Models\Windrow;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index()
    {
        $stats = $this->getSummaryStats();
        
        return view('charts.index', compact('stats'));
    }

    public function statsData()
    {
        try {
            return response()->json($this->getSummaryStats());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get stock data grouped by date for last 90 days
     */
    public function stockData()
    {
        try {
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Stock::query(), $range)
                ->select('Date', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->formatDateSeries($data, $range, 'Date', 'total_quantity');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total' => $data->sum('total_quantity'),
                    'average' => round($data->avg('total_quantity') ?? 0, 2),
                    'max' => $data->max('total_quantity') ?? 0,
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Stock::query(), $range)
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Sale::query(), $range)
                ->select('Date', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->formatDateSeries($data, $range, 'Date', 'total_amount');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_amount' => $data->sum('total_amount'),
                    'average_amount' => round($data->avg('total_amount') ?? 0, 2),
                    'max_amount' => $data->max('total_amount') ?? 0,
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Sale::query(), $range)
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(SupplyItem::query(), $range)
                ->select('Date', DB::raw('SUM(cost) as total_cost'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->formatDateSeries($data, $range, 'Date', 'total_cost');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_cost' => $data->sum('total_cost'),
                    'average_cost' => round($data->avg('total_cost') ?? 0, 2),
                    'max_cost' => $data->max('total_cost') ?? 0,
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(SupplyItem::query(), $range)
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
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Logistics::query(), $range, 'start_time')
                ->whereNotNull('start_time')
                ->select(
                    DB::raw('DATE(start_time) as Date'),
                    DB::raw('SUM(CAST(running_kms AS DECIMAL(10,2))) as total_km'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('DATE(start_time)'))
                ->orderBy('Date', 'asc')
                ->get();

            $result = $this->formatDateSeries($data, $range, 'Date', 'total_km');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total_km' => $data->sum('total_km'),
                    'average_km' => round($data->avg('total_km') ?? 0, 2),
                    'max_km' => $data->max('total_km') ?? 0,
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
            $range = $this->dateRange();
            
            $sales = $this->applyDateRange(Sale::query(), $range)
                ->select('Date', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get()
                ->keyBy('Date');
                
            $costs = $this->applyDateRange(SupplyItem::query(), $range)
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
                        'label' => 'Revenue',
                        'data' => $salesData,
                        'borderColor' => 'rgb(14, 165, 233)',
                        'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    ],
                    [
                        'label' => 'Costs',
                        'data' => $costsData,
                        'borderColor' => 'rgb(245, 158, 11)',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    ],
                    [
                        'label' => 'Profit',
                        'data' => $profitData,
                        'borderColor' => 'rgb(16, 185, 129)',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
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
     * Get vehicle time data (start/end times) grouped by date and vehicle
     */
    public function vehicleTimeData()
    {
        try {
            $range = $this->dateRange();
            $vehicleId = request()->query('vehicle_id');
            
            $query = $this->applyDateRange(Logistics::query(), $range, 'start_time')
                ->with('vehicle')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->orderBy('start_time', 'asc');
            
            if ($vehicleId) {
                $query->where('vehicle_id', $vehicleId);
            }
            
            $trips = $query->get();
            
            // Group by vehicle
            $vehicles = $trips->groupBy('vehicle_id');
            
            $datasets = [];
            foreach ($vehicles as $vehicleId => $vehicleTrips) {
                $vehicle = $vehicleTrips->first()->vehicle;
                $vehicleName = $vehicle ? $vehicle->registration_number : "Vehicle {$vehicleId}";
                
                $startTimes = [];
                $endTimes = [];
                $labels = [];
                
                foreach ($vehicleTrips as $trip) {
                    $labels[] = date('M d', strtotime($trip->start_time));
                    $startTimes[] = date('H:i:s', strtotime($trip->start_time));
                    $endTimes[] = date('H:i:s', strtotime($trip->end_time));
                }
                
                $datasets[] = [
                    'label' => $vehicleName,
                    'vehicle_id' => $vehicleId,
                    'start_times' => $startTimes,
                    'end_times' => $endTimes,
                    'labels' => $labels,
                ];
            }
            
            return response()->json([
                'datasets' => $datasets,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get vehicle distance comparison data
     */
    public function vehicleDistanceComparison()
    {
        try {
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Logistics::query(), $range, 'start_time')
                ->with('vehicle')
                ->whereNotNull('start_time')
                ->select(
                    DB::raw('DATE(start_time) as Date'),
                    'vehicle_id',
                    DB::raw('SUM(CAST(running_kms AS DECIMAL(10,2))) as total_km')
                )
                ->groupBy(DB::raw('DATE(start_time)'), 'vehicle_id')
                ->orderBy('Date', 'asc')
                ->get();
            
            // Group by vehicle
            $vehicles = $data->groupBy('vehicle_id');
            
            $allDates = $data->pluck('Date')->unique()->sort()->values();
            $labels = $allDates->map(fn($date) => date('M d', strtotime($date)))->toArray();
            
            $datasets = [];
            $colors = [
                ['border' => 'rgb(14, 165, 233)', 'bg' => 'rgba(14, 165, 233, 0.1)'],
                ['border' => 'rgb(245, 158, 11)', 'bg' => 'rgba(245, 158, 11, 0.1)'],
                ['border' => 'rgb(16, 185, 129)', 'bg' => 'rgba(16, 185, 129, 0.1)'],
                ['border' => 'rgb(239, 68, 68)', 'bg' => 'rgba(239, 68, 68, 0.1)'],
            ];
            
            foreach ($vehicles as $index => $vehicleTrips) {
                $vehicle = $vehicleTrips->first()->vehicle;
                $vehicleName = $vehicle ? $vehicle->registration_number : "Vehicle {$vehicleTrips->first()->vehicle_id}";
                
                $kmData = [];
                foreach ($allDates as $date) {
                    $trip = $vehicleTrips->firstWhere('Date', $date);
                    $kmData[] = $trip ? (float)$trip->total_km : 0;
                }
                
                $colorIndex = $index % count($colors);
                $datasets[] = [
                    'label' => $vehicleName,
                    'data' => $kmData,
                    'borderColor' => $colors[$colorIndex]['border'],
                    'backgroundColor' => $colors[$colorIndex]['bg'],
                ];
            }
            
            return response()->json([
                'labels' => $labels,
                'datasets' => $datasets,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get stock product list with quantity and value
     */
    public function stockProductListQtyValue()
    {
        try {
            $products = Product::get();
            
            $labels = [];
            $quantities = [];
            $values = [];
            
            foreach ($products as $product) {
                $labels[] = $product->name;
                $qty = (float)$product->getCurrentStock();
                $quantities[] = $qty;
                $values[] = round($qty * (float)$product->price, 2);
            }
            
            return response()->json([
                'labels' => $labels,
                'quantities' => $quantities,
                'values' => $values,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get consumable costs grouped by month
     */
    public function consumablesCostByMonth()
    {
        try {
            $range = $this->dateRange();
            
            $query = SupplyItem::query();
            $query = $this->applyDateRange($query, $range);
            
            $data = $query->select('Date', 'cost')
                ->orderBy('Date', 'asc')
                ->get();
            
            $grouped = $data->groupBy(function($item) {
                return date('Y-m', strtotime($item->Date));
            });
            
            $labels = [];
            $costs = [];
            
            foreach ($grouped as $month => $items) {
                $labels[] = date('M Y', strtotime($month . '-01'));
                $costs[] = round($items->sum('cost'), 2);
            }
            
            return response()->json([
                'labels' => $labels,
                'costs' => $costs,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Parse duration string to hours (float)
     */
    private function parseDurationToHours($str)
    {
        if (empty($str)) return 0;
        $str = strtolower($str);
        if (is_numeric($str)) return (float)$str;
        
        $hours = 0;
        $minutes = 0;
        
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:hour|hr)/', $str, $matches)) {
            $hours = (float)$matches[1];
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:minute|min)/', $str, $matches)) {
            $minutes = (float)$matches[1];
        }
        
        if ($hours == 0 && $minutes == 0) {
            if (preg_match('/(\d+(?:\.\d+)?)/', $str, $matches)) {
                return (float)$matches[1];
            }
        }
        
        return $hours + ($minutes / 60);
    }

    /**
     * Get turning duration vs date
     */
    public function turningData()
    {
        try {
            $range = $this->dateRange();
            
            $query = Turning::query();
            $query = $this->applyDateRange($query, $range, 'Date');
            
            $turnings = $query->orderBy('Date', 'asc')->get();
            
            $grouped = $turnings->groupBy('Date');
            $data = collect();
            
            foreach ($grouped as $date => $records) {
                $totalDuration = 0;
                foreach ($records as $record) {
                    $totalDuration += $this->parseDurationToHours($record->duration);
                }
                $data->push([
                    'Date' => $date,
                    'total_duration' => round($totalDuration, 2)
                ]);
            }
            
            $result = $this->formatDateSeries($data, $range, 'Date', 'total_duration');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total' => round($data->sum('total_duration'), 2),
                    'average' => round($data->avg('total_duration') ?? 0, 2),
                    'max' => round($data->max('total_duration') ?? 0, 2),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get active windrows count vs date
     */
    public function windrowData()
    {
        try {
            $range = $this->dateRange();
            
            if (($range['type'] ?? 'all') === 'all') {
                $startDate = Windrow::min('start_date') ?? now()->subDays(30)->format('Y-m-d');
                $endDate = now()->format('Y-m-d');
            } else {
                $startDate = $range['start'];
                $endDate = $range['end'];
            }
            
            // Fetch windrows overlapping with the date range
            $windrows = Windrow::whereNotNull('start_date')
                ->where('start_date', '<=', $endDate)
                ->where(function($q) use ($startDate) {
                    $q->where('end_date', '>=', $startDate)
                      ->orWhere('out_date', '>=', $startDate)
                      ->orWhere(function($sq) {
                          $sq->whereNull('end_date')->whereNull('out_date');
                      });
                })
                ->orderBy('windrow_number', 'asc')
                ->orderBy('start_date', 'asc')
                ->get();
            
            $data = [];
            foreach ($windrows as $w) {
                $start = $w->start_date;
                $end = $w->end_date ?: ($w->out_date ?: date('Y-m-d'));
                
                $data[] = [
                    'y' => 'Windrow ' . $w->windrow_number,
                    'x' => [$start, $end],
                    'start_date' => date('M d, Y', strtotime($start)),
                    'end_date' => date('M d, Y', strtotime($end)),
                    'is_active' => empty($w->end_date) && empty($w->out_date),
                ];
            }
            
            // Get unique windrow labels sorted numerically
            $labels = $windrows->pluck('windrow_number')
                ->unique()
                ->sort()
                ->values()
                ->map(fn($num) => 'Windrow ' . $num)
                ->toArray();
            
            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'min_date' => $startDate,
                'max_date' => $endDate,
                'summary' => [
                    'current_active' => Windrow::whereNotNull('start_date')
                        ->whereNull('end_date')
                        ->whereNull('out_date')
                        ->count(),
                    'total_recorded' => Windrow::count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get JCB duration vs date
     */
    public function jcbData()
    {
        try {
            $range = $this->dateRange();
            
            $query = Jcb::query();
            $query = $this->applyDateRange($query, $range, 'Date');
            
            $jcbs = $query->orderBy('Date', 'asc')->get();
            
            $grouped = $jcbs->groupBy('Date');
            $data = collect();
            
            foreach ($grouped as $date => $records) {
                $totalDuration = 0;
                foreach ($records as $record) {
                    $totalDuration += $this->parseDurationToHours($record->duration);
                }
                $data->push([
                    'Date' => $date,
                    'total_duration' => round($totalDuration, 2)
                ]);
            }
            
            $result = $this->formatDateSeries($data, $range, 'Date', 'total_duration');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total' => round($data->sum('total_duration'), 2),
                    'average' => round($data->avg('total_duration') ?? 0, 2),
                    'max' => round($data->max('total_duration') ?? 0, 2),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get tare weight transported vs date
     */
    public function weightData()
    {
        try {
            $range = $this->dateRange();
            
            $data = $this->applyDateRange(Weight::query(), $range, 'Date')
                ->select('Date', DB::raw('SUM(tare_weight) as total_tare_weight'))
                ->groupBy('Date')
                ->orderBy('Date', 'asc')
                ->get();
                
            $result = $this->formatDateSeries($data, $range, 'Date', 'total_tare_weight');
            
            return response()->json([
                'labels' => $result['labels'],
                'data' => $result['data'],
                'summary' => [
                    'total' => $data->sum('total_tare_weight'),
                    'average' => round($data->avg('total_tare_weight') ?? 0, 2),
                    'max' => $data->max('total_tare_weight') ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getSummaryStats()
    {
        $range = $this->dateRange();
        $sales = $this->applyDateRange(Sale::query(), $range)->sum('amount') ?? 0;
        $cost = $this->applyDateRange(SupplyItem::query(), $range)->sum('cost') ?? 0;
        $profit = $sales - $cost;

        return [
            'total_sales' => $sales,
            'total_cost' => $cost,
            'total_profit' => $profit,
            'profit_margin' => $sales > 0 ? round(($profit / $sales) * 100, 2) : 0,
            'total_vehicles_km' => $this->applyDateRange(Logistics::query(), $range, 'start_time')->sum(DB::raw('CAST(running_kms AS DECIMAL(10,2))')) ?? 0,
            'products_count' => Product::count(),
            'consumables_count' => Consumable::count(),
        ];
    }

    private function dateRange()
    {
        $days = request()->query('days', 'all');
        $start = request()->query('start');
        $end = request()->query('end');

        if ($start && $end) {
            return [
                'type' => 'custom',
                'start' => date('Y-m-d', strtotime($start)),
                'end' => date('Y-m-d', strtotime($end)),
            ];
        }

        if ($days !== 'all' && is_numeric($days)) {
            $days = max(1, (int) $days);

            return [
                'type' => 'days',
                'days' => $days,
                'start' => now()->subDays($days - 1)->format('Y-m-d'),
                'end' => now()->format('Y-m-d'),
            ];
        }

        return ['type' => 'all'];
    }

    private function applyDateRange($query, array $range, $column = 'Date')
    {
        if (($range['type'] ?? 'all') === 'all') {
            return $query;
        }

        if ($column === 'start_time') {
            return $query->whereBetween($column, [$range['start'] . ' 00:00:00', $range['end'] . ' 23:59:59']);
        }

        return $query->whereBetween($column, [$range['start'], $range['end']]);
    }

    private function formatDateSeries($data, array $range, $dateColumn = 'Date', $dataColumn = 'data')
    {
        if (($range['type'] ?? 'all') === 'all') {
            return [
                'labels' => $data->pluck($dateColumn)->map(fn($date) => date('M d', strtotime($date)))->toArray(),
                'data' => $data->pluck($dataColumn)->toArray(),
            ];
        }

        return $this->fillMissingDates($data, $range['start'], $range['end'], $dateColumn, $dataColumn);
    }

    private function fillMissingDates($data, $startDate, $endDate, $dateColumn = 'Date', $dataColumn = 'data')
    {
        $labels = [];
        $values = [];
        $current = strtotime($startDate);
        $end = strtotime($endDate);

        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $labels[] = date('M d', $current);
            
            $record = $data->firstWhere($dateColumn, $date);
            $values[] = $record?->$dataColumn ?? 0;
            $current = strtotime('+1 day', $current);
        }
        
        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }
}
