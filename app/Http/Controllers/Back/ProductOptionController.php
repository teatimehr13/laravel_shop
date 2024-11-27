<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductOptionRequest;
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
    public function create(ProductOptionRequest $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, ProductOptionRequest $request)
    {
        //要有id進來

        try {
            // Log::info($id);
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = '/storage/' . $request->file('image')->storeAs(
                    'product_options',
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $productOption = ProductOption::create($validated);

            return response()->json($productOption);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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
    public function update($p_id, $po_id, ProductOptionRequest $request)
    {

        try {
            $product_option = ProductOption::find($po_id);

            if (!$product_option) {
                return response()->json(['error' => 'Product option not found'], 404);
            }

            $validated = $request->validated();

            if ($request->hasFile('image')) {
                //delete old
                if ($product_option->image) {
                    $oldImagePath = str_replace('/storage/', '', $product_option->image);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }

                //insert new
                $name = time() . '_' . $request->file('image')->getClientOriginalName(); // 避免檔名重複
                $path = $request->file('image')->storeAs('product_options', $name, 'public');
                $validated['image'] = '/storage/' . $path;
            }

            $product_option->update($validated);
            return response()->json(['success' => true], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($p_id, $po_id)
    {
        // Log::info($po_id);

        $product_option = ProductOption::find($po_id);
        if ($product_option) {
            $image = $product_option->image ? str_replace('/storage/', '', $product_option->image) : '';
            if ($image && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
            $product_option->delete();
            return response()->json(null, 200);
        }

        return response()->json(null, 403);
    }


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
        if ($request->input('po_id')) {
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
