<?php

use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;
use App\Http\Controllers\Back\ProductOptionController as BackProductOptionController;
use App\Http\Controllers\Back\StoreController as BackStoreController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\OrderController as BackOrderController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('back')->group(function () {
        Route::resource('products', BackProductController::class)->only(['index', 'edit']);
        Route::post('/categories/{category_id}/subsel', [BackProductController::class, 'getSubSel']);
        Route::get('/products/{product_id}/prod_options', [BackProductController::class, 'prod_options']);
        Route::get('/products/{id}/images', [BackProductController::class, 'images'])->name('images.show');

        Route::post('/products/reorderProductImgs', [BackProductController::class, 'reorderProductImgs'])->name('reorderProductImgs');

        Route::post('/products/product_images', [BackProductOptionController::class, 'product_images'])->name('product_images');
        Route::get('/product_options/{id}/checkImgs', [BackProductOptionController::class, 'checkProductOptionImages'])->name('checkProductOptionImages');

        Route::resource('categories', BackCategoryController::class)->only(['index']);
        Route::post('/categories/reorder', [BackCategoryController::class, 'reorderCategories']);

        Route::post('/subcategories/reorder', [BackSubcategoryController::class, 'reorderSubcategories']);
        Route::resource('stores', BackStoreController::class)->only(['index']);


        // Route::resource('news', BackNewsController::class);
        // Route::post('/news/update_news', [BackNewsController::class, 'update_news'])->name('news.update_news');
        // Route::post('/news/delete_news', [BackNewsController::class, 'delete_news'])->name('news.delete_news');

        Route::resource('backorder', BackOrderController::class)->only(['index', 'show']);
        Route::put('/backorder/{order}/status', [BackOrderController::class, 'changeOrderStatus']);

        Route::middleware('prevent.visitor')->group(function () {
            Route::resource('products', BackProductController::class)->only(['update', 'destroy', 'store']);
            Route::resource('product_options', BackProductOptionController::class)->only('destroy', 'store', 'update');
            Route::resource('categories', BackCategoryController::class)->only(['update', 'destroy', 'store']);
            Route::resource('stores', BackStoreController::class)->only(['store']);

            Route::post('/products/updateProductImages', [BackProductController::class, 'updateProductImages'])->name('updateProductImages');
            
            Route::resource('subcategories', BackSubcategoryController::class)->only(['destroy']);
            Route::post('/subcategories/{subcategory}/update_sub', [BackSubcategoryController::class, 'updateSub']);
            Route::post('/categories/{category_id}/subcategories', [BackSubcategoryController::class, 'store']);
            Route::post('/stores/update_stores', [BackStoreController::class, 'update_stores'])->name('stores.update_stores');
            Route::post('/stores/delete_stores', [BackStoreController::class, 'delete_stores'])->name('stores.delete_stores');
        });
    });
});
