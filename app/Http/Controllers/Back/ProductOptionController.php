<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductRequest;
use App\Http\Resources\Back\ProductResource;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProductOptionController extends Controller
{
    public function index(Product $product)
    {
        $productOptions = $product->product_options()->get();
        return Inertia::render('Back/ProductOption', [
            'productOptions' => $productOptions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProductRequest $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ProductRequest $request) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}


    public function product_images(Request $request)
    {
        // Log::info(json_encode($request->all()));
        // Log::info($product_id);
        // if($request->input('po_id') && $request->input('pr_id')){
        //     $productOption_id = $request->input('po_id');
        //     $product_id = $request->input('pr_id');
        //     $productOption = ProductOption::where('product_id',$product_id)->where('id',$productOption_id)->first();
        //     Log::info($productOption->productImages);
        // }
        if($request->input('po_id')){
            $productOptionId = $request->input('po_id');
    
            // 根據 productOptionId 獲取相應的產品選項數據
            $productImages = ProductOption::find($productOptionId)->productImages()->get();
    
            $formattedProductImages = $productImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'alt_text' => $image->alt_text,
                    'image' => $image->image,
                    'order' => $image->order,
                ];
            });
    
            // 返回數據給前端
            return response()->json($formattedProductImages);
        }
    }
}
