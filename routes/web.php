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
use App\Models\Customer;
use App\Models\Product;
use App\Models\Society;
use App\Models\Vehicle;
use App\Models\Logisctics;
use App\Models\Weight;
use App\Models\Trip;
use App\Models\Windrow;
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

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
