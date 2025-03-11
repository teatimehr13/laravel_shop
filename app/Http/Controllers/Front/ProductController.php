<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index($search_key)
    {
        // $subcategory = Subcategory::where('search_key', $search_key)->first();
        $subcategory = Subcategory::where('search_key', $search_key)
            ->with('category') // 取得上級分類
            ->firstOrFail();

        if (!$subcategory) {
            return;
        }

        $subcategory_id = $subcategory->id;
        $subcategory_name = $subcategory->name;
        $category_id = $subcategory->category_id;

        $productLists = Product::where('subcategory_id', $subcategory_id)->get();
        $categoryLists = Subcategory::where('category_id', $category_id)
        ->get()
        ->map(function($category_list){
            return [
                'name' => $category_list->name,
                'search_key' => $category_list->search_key,
            ];
        });

        // Log::info($productLists);

        return Inertia::render('Front/ProductList', [
            'productLists' => $productLists,
            'subcategory_name' => $subcategory_name,
            'category' => $subcategory->category,
            'subcategory' => $subcategory,
            'categoryLists' => $categoryLists

        ]);
    }

    public function show(string $slug)
    {
        // $product = Product::find($productId);
        // $product = Product::where('slug', $slug)->firstOrFail();
        $product = Product::where('slug', $slug)
            ->with(['subcategory.category']) // 取得分類 & 子分類
            ->firstOrFail();

        $productId = $product->id;
        // $productOptions = ProductOption::where('product_id', $productId)->get();
        // $productImages = ProductOption::with('productImages')->where('product_id', $productId)->get();
        // $productImages = ProductImage::where('product_id', $productId)->get();
        $productOptions = ProductOption::with('productImages')->where('product_id', $productId)->get();

        return Inertia::render('Front/ProductShow', [
            'product' => $product,
            'productOptions' => $productOptions,
            'category' => $product->subcategory->category, // 上級分類
            'subcategory' => $product->subcategory, // 當前子分類
            // 'productImages' => $productImages
        ]);
    }
}
