<?php

use App\Http\Controllers\Ecpay\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/ecpay/checkout', [PaymentController::class, 'checkout'])->name('ecpay.checkout');
Route::post('/ecpay/return', [PaymentController::class,'returnUrl']);
// Route::post('/ecpay/return', [PaymentController::class, 'return'])->name('ecpay.return');
// Route::get ('/ecpay/result',   [PaymentController::class, 'front'])->name('ecpay.front');