<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\ProductOptionController as BackProductOptionController;
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

Route::middleware('auth')->group(function () {
    Route::get('/cart', function () {
        return Inertia::render('Front/Cart');
    })->name('cart');


    Route::prefix('back')->group(function () {
        Route::resource('products.productOptions', BackProductOptionController::class)
            ->only(['index'])
            ->names([
                'index' => 'back.products.productOptions.index'
            ]);
        // Route::get('/products', function () {
        //     return Inertia::render('Back/Product');
        // })->name('products');

        Route::resource('products', BackProductController::class);
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