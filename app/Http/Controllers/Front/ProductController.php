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
    public function index(Request $request, $search_key)
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

        // $productLists = Product::where('subcategory_id', $subcategory_id)->get();

        //建立 query builder
        $query = Product::where('subcategory_id', $subcategory_id);

        //加入排序條件
        $sort_by = $request->input('sort_by', 'created_at'); // 預設用時間
        $sort_dir = $request->input('sort_dir', 'desc');     // 預設 desc

        $validSorts = ['created_at', 'price'];
        $validDirs = ['asc', 'desc'];

        if (in_array($sort_by, $validSorts) && in_array($sort_dir, $validDirs)) {
            $query->orderBy($sort_by, $sort_dir);
        }

        $productLists = $query->get();

        // Log::info('sort_by', [$sort_by]);
        // Log::info('query sql', [$query->toSql()]);
        // Log::info('bindings', $query->getBindings());


        $categoryLists = Subcategory::where('category_id', $category_id)
            ->get()
            ->map(function ($category_list) {
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
            'categoryLists' => $categoryLists,
            'filters' => [
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
            ]

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
