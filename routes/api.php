<?php

//back
use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;
use App\Http\Controllers\Back\ProductOptionController as BackProductOptionController;

use App\Http\Controllers\ReturnRequestController;

use App\Http\Controllers\CartController;
use App\Libraries\UserAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth')->get('/user', function (Request $request) {
    return response()->json($request->user());
});


//back admin
// Route::resource('products', BackProductController::class)->except(['create', 'edit']);
// Route::resource('products.productOptions', BackProductController::class)->except(['create', 'edit']);
// Route::resource('productOptions', BackProductController::class)->names(
//     [
//         'product_images' => 'productImages'
//     ]
// );

// Route::post('/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');
Route::prefix('back')->name('back.')->group(function () {
    Route::post('/products/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');
    Route::post('/products/updateProductImages', [BackProductOptionController::class, 'updateProductImages'])->name('updateProductImages');
    // Route::resource('products', BackProductController::class)->except(['create', 'edit']);

    Route::resource('products.productOptions', BackProductOptionController::class)->except(['create', 'edit', 'index']);
});


// Route::resource('categories', BackCategoryController::class)->only(['store', 'update']);
// Route::resource('categories.subcategories', BackSubcategoryController::class)->except(['show', 'create', 'edit']);

//cart
Route::prefix('cart')->name('cart.')->group(function () {
    // Route::get('/', [CartController::class, 'index'])->name('index');
    // Route::post('/addToCart', [CartController::class, 'addToCart'])->name('addToCart');
    Route::middleware('web')->get('/', [CartController::class, 'index']);
    // Route::middleware('web')->post('/addToCart', [CartController::class, 'addToCart']);
    // Route::middleware('web')->delete('/deleteCartItem', [CartController::class, 'deleteCartItem']);
    // Route::middleware('web')->post('/updateCartItem', [CartController::class, 'updateCartItem']);
    // Route::middleware('web')->get('/getCartFromCookie', [CartController::class, 'getCartFromCookie']);
    // Route::middleware('web')->get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

//auth
Route::middleware(['web'])->group(function () {
    Route::post('/login_access', function (Request $request) {
        return UserAuth::login($request);
    });

    Route::post('/logout', function (Request $request) {
        return UserAuth::logout($request);
    });
});


// Route::post('/returnRequest', [ReturnRequestController::class, 'store'])->name('returnRequest');