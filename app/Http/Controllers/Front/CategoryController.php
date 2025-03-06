<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $products = Product::all();
        // $categories = Category::with('subcategories.firstProductImage')->get();

        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->where('show_in_list', 1);
                $query->orderBy('order_index', 'asc');
            },
            'subcategories.firstProductImage'
        ])
            ->get() //將結果轉為 Eloquent Collection
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    //map() 作用於 get() 後，Eloquent Collection 變成普通 Collection
                    //如果 subcategories 為 null，直接訪問 $category->subcategories->map() 會報錯
                    //使用 collect($category->subcategories)，確保即使 subcategories 為 null，它也會變成空 Collection
                    'subcategories' => collect($category->subcategories)->map(function ($subcategory) {
                        return [
                            'id' => $subcategory->id,
                            'name' => $subcategory->name,
                            'image' => optional($subcategory->firstProductImage)->image, // optional接受null 避免報錯
                            'search_key' => $subcategory->search_key
                        ];
                    })->values()
                ];
            });


        return Inertia::render('Front/Category', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

}
