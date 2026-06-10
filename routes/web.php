<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\WeightController;
use App\Http\Controllers\TripsController;
use App\Http\Controllers\WindrowController;
use App\Http\Controllers\TurningController;
use App\Http\Controllers\JcbController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\SupplyItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InvoiceDispatchController;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Society;
use App\Models\Vehicle;
use App\Models\Logisctics;
use App\Models\Weight;
use App\Models\Trip;
use App\Models\Windrow;
use App\Models\Turning;
use App\Models\Jcb;
use App\Models\Consumable;
use App\Models\SupplyItem;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Dashboard route - shows the main dashboard
Route::get('/dashboard', function () {
    $userId = auth()->id();

    return view('dashboard', [
        'societiesCount' => Society::where('user_id', $userId)->count(),
        'vehiclesCount' => Vehicle::where('user_id', $userId)->count(),
        'customersCount' => Customer::where('user_id', $userId)->count(),
        'productsCount' => Product::where('user_id', $userId)->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('societies', SocietyController::class)->except('show');
    Route::resource('vehicles', VehicleController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('logistics', LogisticsController::class)->except('show');
    Route::resource('weights', WeightController::class)->except('show');
    Route::resource('trips', TripsController::class)->except('show');
    Route::resource('windrow', WindrowController::class)->except('show');
    Route::resource('turning', TurningController::class)->except('show');
    Route::resource('jcb', JcbController::class)->except('show');
    Route::resource('consumables', ConsumableController::class)->except('show');
    Route::resource('supplyitems', SupplyItemController::class)->except('show');
    Route::resource('sale', SaleController::class)->except('show');
    Route::resource('stock', StockController::class)->except('show');
    Route::resource('tasks', TaskController::class)->except('show');
    Route::post('/stock/sync-sales', [StockController::class, 'syncSales'])->name('stock.sync-sales');

    Route::get('/get_product_rate/ajax/{product_id}',[ProductController::class, 'getProductRate']);

    // Charts Routes
    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/api/chart-stats', [ChartController::class, 'statsData']);
    Route::get('/api/stock-data', [ChartController::class, 'stockData']);
    Route::get('/api/stock-data-by-product', [ChartController::class, 'stockDataByProduct']);
    Route::get('/api/sale-data', [ChartController::class, 'saleData']);
    Route::get('/api/sale-data-by-product', [ChartController::class, 'saleDataByProduct']);
    Route::get('/api/cost-data', [ChartController::class, 'costData']);
    Route::get('/api/cost-data-by-consumable', [ChartController::class, 'costDataByConsumable']);
    Route::get('/api/vehicle-data', [ChartController::class, 'vehicleData']);
    Route::get('/api/profit-loss-data', [ChartController::class, 'profitLossData']);
    Route::get('/api/vehicle-time-data', [ChartController::class, 'vehicleTimeData']);
    Route::get('/api/vehicle-distance-comparison', [ChartController::class, 'vehicleDistanceComparison']);
    Route::get('/api/stock-product-qty-value', [ChartController::class, 'stockProductListQtyValue']);
    Route::get('/api/consumables-cost-by-month', [ChartController::class, 'consumablesCostByMonth']);
    Route::get('/api/turning-data', [ChartController::class, 'turningData']);
    Route::get('/api/windrow-data', [ChartController::class, 'windrowData']);
    Route::get('/api/jcb-data', [ChartController::class, 'jcbData']);
    Route::get('/api/weight-data', [ChartController::class, 'weightData']);

    // Invoice dispatch dashboard routes
    Route::get('/invoices', [InvoiceDispatchController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/report', [InvoiceDispatchController::class, 'getReportData'])->name('invoices.report');
    Route::get('/invoices/download-zip', [InvoiceDispatchController::class, 'downloadZip'])->name('invoices.download-zip');
    Route::get('/invoices/stats-details', [InvoiceDispatchController::class, 'getStatsDetails'])->name('invoices.stats-details');
    Route::get('/invoices/societies-list', [InvoiceDispatchController::class, 'getSocietiesForDispatch'])->name('invoices.societies-list');
    Route::get('/invoices/society/{society}/pdf', [InvoiceDispatchController::class, 'viewPdfBySociety'])->name('invoices.society-pdf');
    Route::post('/invoices/global-dispatch', [InvoiceDispatchController::class, 'triggerGlobalDispatch'])->name('invoices.global-dispatch');
    Route::post('/invoices/dispatch-one/{society}', [InvoiceDispatchController::class, 'dispatchOne'])->name('invoices.dispatch-one');
    Route::post('/invoices/retry-failed', [InvoiceDispatchController::class, 'retryFailed'])->name('invoices.retry-failed');
    Route::post('/invoices/retry-single/{society}', [InvoiceDispatchController::class, 'retrySingle'])->name('invoices.retry-single');
    Route::post('/invoices/generate-global', [InvoiceDispatchController::class, 'generateGlobal'])->name('invoices.generate-global');
    Route::post('/invoices/clear-pending', [InvoiceDispatchController::class, 'clearPending'])->name('invoices.clear-pending');
    Route::post('/invoices/clear-queue', [InvoiceDispatchController::class, 'clearQueue'])->name('invoices.clear-queue');
    Route::get('/invoices/{invoice}/pdf', [InvoiceDispatchController::class, 'viewPdf'])->name('invoices.pdf');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('units', UnitController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
