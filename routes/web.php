<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/inventory/products');
    }
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Inventory Routes (Protected)
Route::prefix('inventory')->middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('materials', MaterialController::class);
    Route::resource('orders', OrderController::class);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// API Routes
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('products/{product}/materials', function ($productId) {
        $product = \App\Models\Product::with('materials')->find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'materials' => $product->materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'quantity' => $material->quantity,
                    'quantity_needed' => $material->quantity_needed,
                ];
            })
        ]);
    });
});
