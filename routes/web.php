<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;

Route::get('payment', [PaymentController::class, 'index']);
Route::post('payment/charge', [PaymentController::class, 'charge']);
Route::get('paymentIsSuccess', [PaymentController::class, 'paymentIssuccess']);