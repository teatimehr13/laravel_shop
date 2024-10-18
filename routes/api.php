<?php

//back
use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;

use App\Http\Controllers\CartController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//back admin
Route::resource('products', BackProductController::class)->except(['create', 'edit']);
Route::resource('categories', BackCategoryController::class)->except(['create', 'edit']);
Route::resource('categories.subcategories', BackSubcategoryController::class)->except(['show','create', 'edit']);

//cart
Route::prefix('cart')->name('cart.')->group(function(){
    // Route::get('/', [CartController::class, 'index'])->name('index');
    // Route::post('/addToCart', [CartController::class, 'addToCart'])->name('addToCart');
    Route::middleware('web')->get('/', [CartController::class, 'index']);
    Route::middleware('web')->post('/addToCart', [CartController::class, 'addToCart']);
    Route::middleware('web')->delete('/deleteCartItem', [CartController::class, 'deleteCartItem']);
    Route::middleware('web')->post('/updateCartItem', [CartController::class, 'updateCartItem']);
    Route::middleware('web')->get('/getCartFromCookie', [CartController::class, 'getCartFromCookie']);
    // Route::delete('/deleteCartItem', [CartController::class, 'deleteCartItem'])->name('deleteCartItem');
    // Route::patch('/updateCartItems', [CartController::class, 'updateCartItems'])->name('updateCartItems');
    // Route::middleware(['auth'])->get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});