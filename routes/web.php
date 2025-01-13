<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;
use App\Http\Controllers\Back\ProductOptionController as BackProductOptionController;
use App\Http\Controllers\Back\StoreController as BackStoreController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
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

        Route::resource('products', BackProductController::class)->only(['index']);
        Route::resource('stores', BackStoreController::class);
        Route::resource('categories', BackCategoryController::class)->only(['index']);
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

// Route::post('/back/products/{product_id}/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');

// Route::post('/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');

require __DIR__ . '/auth.php';


// Route::get('/products', function () {
//     return Inertia::render('Products', [
//         'isLoggedIn' => Auth::check(),
//         'user' => Auth::user(),
//     ]);
// })->middleware('auth')->name('products');