<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/admin', [OrderController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/orders', [OrderController::class, 'store'])->name('admin.orders.store');
Route::get('/admin/orders/json', [OrderController::class, 'getOrders'])->name('admin.orders.json');
Route::post('/admin/orders/{order}/status/{status}', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
Route::put('/admin/orders/{order}', [OrderController::class, 'update'])->name('admin.orders.update');
Route::delete('/admin/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
Route::get('/tracking/{orderNumber}', [TrackingController::class, 'show'])->name('tracking.show');
