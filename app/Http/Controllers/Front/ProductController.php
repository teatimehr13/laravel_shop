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
        $subcategory = Subcategory::where('search_key', $search_key)->first();
        if(!$subcategory){
            return;
        }

        $subcategory_id = $subcategory->id;
        $subcategory_name = $subcategory->name; 

        $productLists = Product::where('subcategory_id', $subcategory_id)->get();

        // Log::info($productLists);

        return Inertia::render('Front/ProductList', [
            'productLists' => $productLists,
            'subcategory_name' => $subcategory_name

        ]);
    }

    public function show(string $productId){
        $product = Product::find($productId);
        // $productOptions = ProductOption::where('product_id', $productId)->get();
        // $productImages = ProductOption::with('productImages')->where('product_id', $productId)->get();
        // $productImages = ProductImage::where('product_id', $productId)->get();
        $productOptions = ProductOption::with('productImages')->where('product_id', $productId)->get();

        return Inertia::render('Front/ProductShow', [
            'product' => $product,
            'productOptions' => $productOptions,
            // 'productImages' => $productImages
        ]);
    }

    
}
