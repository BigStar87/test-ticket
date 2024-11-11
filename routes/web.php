<?php

use App\Http\Controllers\DataTicketController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('store', [DataTicketController::class, 'orderData']);
Route::post('/orders', [OrderController::class, 'store']);


