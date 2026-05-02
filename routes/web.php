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
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\SupplyItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ChartController;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Society;
use App\Models\Vehicle;
use App\Models\Logisctics;
use App\Models\Weight;
use App\Models\Trip;
use App\Models\Windrow;
use App\Models\Turning;
use App\Models\Consumable;
use App\Models\SupplyItem;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

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
    Route::resource('consumables', ConsumableController::class)->except('show');
    Route::resource('supplyitems', SupplyItemController::class)->except('show');
    Route::resource('sale', SaleController::class)->except('show');
    Route::resource('stock', StockController::class)->except('show');

    Route::get('/get_product_rate/ajax/{product_id}',[ProductController::class, 'getProductRate']);

    // Charts Routes
    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/api/stock-data', [ChartController::class, 'stockData']);
    Route::get('/api/sale-data', [ChartController::class, 'saleData']);
    Route::get('/api/cost-data', [ChartController::class, 'costData']);
    Route::get('/api/vehicle-data', [ChartController::class, 'vehicleData']);

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');

        // Unit Management Routes
        Route::get('/admin/units', [UnitController::class, 'index']);
        Route::get('/admin/unit-form/{unit_id}', [UnitController::class, 'addEditUnit']);
        Route::post('/admin/saveunit', [UnitController::class, 'save']);
        Route::post('/admin/unit/delete', [UnitController::class, 'deleteUnit']);
        Route::get('/admin/unit/{unit_id}', [UnitController::class, 'viewUnit']);
    });
});

require __DIR__.'/auth.php';
