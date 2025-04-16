<?php

use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReturnRequestController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;
use App\Http\Controllers\Back\ProductOptionController as BackProductOptionController;
use App\Http\Controllers\Back\StoreController as BackStoreController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
use App\Models\Order;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/cart', function () {
    return Inertia::render('Front/Cart');
})->name('cart');

Route::get('/product_show', function () {
    return Inertia::render('Front/Product_show');
})->name('product_show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.front.index');

Route::get('/product/list/{search_key}', [ProductController::class, 'index'])->name('product.front.index');
Route::get('/product/show/{slug}', [ProductController::class, 'show'])->name('product.front.show');


Route::middleware('auth')->group(function () {
    Route::prefix('back')->group(function () {
        Route::resource('products.productOptions', BackProductOptionController::class)
            ->only(['index'])
            ->names([
                'index' => 'back.products.productOptions.index'
            ]);

        // Route::get('/products', function () {
        //     return Inertia::render('Back/Product');
        // })->name('products');

        Route::resource('products', BackProductController::class)->only(['index', 'update', 'destroy', 'store']);
        Route::post('/categories/{category_id}/subsel', [BackProductController::class, 'getSubSel']);
        Route::get('/products/{product_id}/prod_options', [BackProductController::class, 'prod_options']);
        // Route::post('/product_options/{product_option_id}/updateProdCo', [BackProductController::class, 'updateProdCo']);
        // Route::post('/product_options/addProdCo', [BackProductController::class, 'addProdCo']);
        // Route::post('/product_options/{product_option_id}', [BackProductController::class, 'delProdCo']);

        Route::resource('product_options', BackProductOptionController::class)->only('destroy', 'store', 'update');
        Route::post('/products/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');
        Route::post('/products/updateProductImages', [BackProductOptionController::class, 'updateProductImages'])->name('updateProductImages');
        // checkProductOptionImages
        Route::get('/product_options/{id}/checkImgs', [BackProductOptionController::class, 'checkProductOptionImages'])->name('checkProductOptionImages');

        Route::resource('stores', BackStoreController::class);
        Route::resource('categories', BackCategoryController::class)->only(['index', 'update', 'destroy', 'store']);
        Route::post('/categories/reorder', [BackCategoryController::class, 'reorderCategories']);

        // Route::post('/subcategories/update_sub', [BackSubcategoryController::class, 'updateSub'])->name('subcategories.updateSub');
        Route::resource('subcategories', BackSubcategoryController::class)->only(['destroy']);
        Route::post('/subcategories/{subcategory}/update_sub', [BackSubcategoryController::class, 'updateSub']);
        Route::post('/categories/{category_id}/subcategories', [BackSubcategoryController::class, 'store']);
        Route::post('/subcategories/reorder', [BackSubcategoryController::class, 'reorderSubcategories']);

        Route::post('/stores/update_stores', [BackStoreController::class, 'update_stores'])->name('stores.update_stores');
        Route::post('/stores/delete_stores', [BackStoreController::class, 'delete_stores'])->name('stores.delete_stores');

        Route::resource('news', BackNewsController::class);
        Route::post('/news/update_news', [BackNewsController::class, 'update_news'])->name('news.update_news');
        Route::post('/news/delete_news', [BackNewsController::class, 'delete_news'])->name('news.delete_news');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::get('/getCartFromCookie', [CartController::class, 'getCartFromCookie']);
    Route::get('/getCartItems', [CartController::class, 'getCartItems']);
    Route::patch('/updateCartItem', [CartController::class, 'updateCartItem']);
    Route::delete('/deleteCartItem', [CartController::class, 'deleteCartItem']);
    Route::get('/', [CartController::class, 'index']);
});


Route::get('/checkout', [CheckoutController::class, 'index']);
Route::post('/checkout/placeOrder', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
Route::get('/order/fetchOrderData/{order_number}', [OrderController::class, 'fetchOrderData'])->name('order.fetchOrderData');
// Route::resource('order', [OrderController::class, 'order'])->only(['index', 'show']);


// Route::post('/returnRequest', [ReturnRequestController::class, 'store'])->name('returnRequest');
Route::post('/return/returnRequest', [ReturnRequestController::class, 'store'])->name('return.returnRequest');
Route::get('/return/return-history/{orderId}', [ReturnRequestController::class, 'fetch_return_history']);

require __DIR__ . '/auth.php';


// Route::get('/products', function () {
//     return Inertia::render('Products', [
//         'isLoggedIn' => Auth::check(),
//         'user' => Auth::user(),
//     ]);
// })->middleware('auth')->name('products');