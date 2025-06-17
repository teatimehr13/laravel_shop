<?php

use App\Http\Controllers\Ecpay\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/checkoutTest', fn() => Inertia::render('Front/CheckoutTest'))->name('checkoutTest');
Route::post('/ecpay/checkout', [PaymentController::class, 'checkout'])->name('ecpay.checkout');
Route::post('/ecpay/return', [PaymentController::class,'returnUrl'])->name('ecpay.return');
Route::post ('/ecpay/result',   [PaymentController::class, 'frontOrderResultURL'])->name('ecpay.result');
// Route::post('/ecpay/return', [PaymentController::class, 'return'])->name('ecpay.return');