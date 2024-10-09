<?php

use App\Http\Controllers\Back\ProductController as BackProductController;
use App\Http\Controllers\Back\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\SubcategoryController as BackSubcategoryController;

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