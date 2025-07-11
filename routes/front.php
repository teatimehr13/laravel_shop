<?php

use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReturnRequestController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/product/list/{search_key}', [ProductController::class, 'index'])->name('product.front.index');
Route::get('/product/show/{slug}', [ProductController::class, 'show'])->name('product.front.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.front.index');

Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/order/fetchOrderData/{order_number}', [OrderController::class, 'fetchOrderData'])->name('order.fetchOrderData');
Route::patch('/order/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');

// Route::middleware('auth')->group(function () {
    Route::post('/return/returnRequest', [ReturnRequestController::class, 'store'])->name('return.returnRequest');
    Route::get('/return/return-history/{orderId}', [ReturnRequestController::class, 'fetch_return_history']);

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/placeOrder', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
// });


Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::get('/getCartFromCookie', [CartController::class, 'getCartFromCookie']);
    Route::get('/getCartItems', [CartController::class, 'getCartItems']);
    Route::patch('/updateCartItem', [CartController::class, 'updateCartItem']);
    Route::delete('/deleteCartItem', [CartController::class, 'deleteCartItem']);
    Route::get('/', [CartController::class, 'index'])->name('cart');
});
