<?php

// namespace App\Http\Controllers;

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductOptionRequest;
use App\Models\Product;
use App\Models\ProductImage;
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
    public function store(ProductOptionRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['published_status'] = 1;
            $validated['quantity'] = 10;


            if ($validated['color_name'] === '組合色') {
                $existingCombination = ProductOption::where('product_id', $validated['product_id'])
                    ->where('color_name', '組合色')
                    ->exists();

                if ($existingCombination) {
                    return response()->json([
                        'message' => '該產品組合色已存在，無法新增。',
                        'comboExist' => true
                    ], 422);
                }
            }

            $product_option = ProductOption::create($validated);


            if ($request->hasFile('image')) {
                $name = time() . '_' . $request->file('image')->getClientOriginalName(); //避免檔名重複
                $path = '/storage/' . $request->file('image')->storeAs(
                    'product_options/' . $product_option->product_id,
                    $name,
                    'public'
                );
                // $validated["image"] = $path;
                $product_option->update([
                    'image' => $path
                ]);
            }

            // $product_option = ProductOption::create($validated);
            return response()->json($product_option, 201); // 回傳成功創建的產品資料
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        };
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
    public function update($po_id, ProductOptionRequest $request)
    {
        try {
            $product_option = ProductOption::find($po_id);
            $validated = $request->validated();

            //收到delete_image為true時刪掉，為false時不刪 (例如有某照片誤傳，但又沒有適合的圖時)
            if ($request->has('delete_image') && $request->input('delete_image') == true) {
                $path = str_replace('/storage/', '', $product_option->image);
                Storage::disk('public')->delete($path);
                $validated['image'] = null; // 將 image 字段設為 null
            } else {
                unset($validated['image']);
            }

            if (isset($validated['image'])) {
                unset($validated['image']);

                Storage::disk('public')->delete(
                    str_replace(
                        '/storage/',
                        '',
                        $product_option->image
                    )
                );
            }


            if ($request->hasFile('image')) {
                //有新圖片一律把舊的刪掉
                if ($product_option->image) {
                    $path = str_replace('/storage/', '', $product_option->image);
                    Storage::disk('public')->delete($path);
                }

                //上傳新圖
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = "/storage/" . $request->file('image')->storeAs(
                    // 'product_options',
                    'product_options/' . $product_option->product_id,
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $product_option->update($validated);

            return response()->json($product_option);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($po_id)
    {
        $product_option = ProductOption::find($po_id);
        if ($product_option) {
            $image = $product_option->image ? str_replace('/storage/', '', $product_option->image) : '';
            if ($image && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }

            if (!$product_option->delete()) {
                return response()->json(['message' => 'failed'], 200);
            }

            return response()->json(['message' => 'success'], 200);
        }
        return response()->json(null, 403);
    }

    //取值拿到產品圖片
    public function product_images(Request $request)
    {
        if ($request->input('pr_id')) {
            $productId = $request->input('pr_id');
            // 根據 productOptionId 獲取相應的產品選項數據
            // $productImages = Product::find($productId)->product_images()->get();
            $productImages = ProductOption::with('productImages')->where('product_id', $productId)->get();

            // 返回數據給前端
            return response()->json($productImages);
        }
    }


    //刪除前確認產品選項的附圖
    public function checkProductOptionImages($po_id)
    {
        // $productOption = ProductOption::with('productImages')->find($po_id);
        $productOption = ProductOption::withCount('productImages')->find($po_id);

        if (!$productOption) {
            return response()->json(['message' => 'Product Option not found']);
        }

        // $imageCount = $productOption->productImages->count();

        return response()->json([
            'image_count' => $productOption->product_images_count
            // 'image_count' => $imageCount
        ]);
    }


    //增刪改版本
    public function updateProductImages(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'product_options' => 'required|array', //支援多個顏色圖片
                'product_options.*.po_id' => 'required|integer|exists:product_options,id',
                'product_options.*.product_images' => 'required|array',
                'product_options.*.product_images.*.id' => 'nullable|integer|exists:product_images,id',
                'product_options.*.product_images.*.alt_text' => 'nullable|string|max:255',
                'product_options.*.product_images.*.is_combination' => 'required|boolean',
                'product_options.*.product_images.*.image' => 'nullable|image',
                'product_options.*.product_images.*._delete' => 'nullable|boolean',
            ]);

            $productId = $validated['product_id'];
            // $validated = [
            //     'product_options' => [
            //         'po_id' => '',
            //         'product_images' => [
            //             'id' => '',
            //             'alt_text' => '',
            //             'is_combination' => ''
            //         ]
            //     ],
            // ];
            // Log::info($validated);

            foreach ($validated['product_options'] as $productOptionData) {
                // 找到對應的 Product Option
                $productOption = ProductOption::findOrFail($productOptionData['po_id']);
                $currentMaxOrder = ProductImage::where('product_id', $productOption->product_id)->max('order') ?? 0;

                // Log::info($productOptionData);
                foreach ($productOptionData['product_images'] as $productImage) {
                    if (!empty($productImage['_delete']) && isset($productImage['id'])) {
                        // 刪除圖片
                        $existingImage = ProductImage::findOrFail($productImage['id']);
                        $path = str_replace('/storage/', '', $existingImage->image);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                        $existingImage->delete();
                    } elseif (isset($productImage['id'])) {
                        // 更新圖片
                        $existingImage = ProductImage::findOrFail($productImage['id']);
                        $existingImage->update([
                            'alt_text' => $productImage['alt_text'],
                            'is_combination' => $productImage['is_combination'],
                        ]);
                    } else {
                        // 新增圖片
                        $path = null;
                        if (isset($productImage['image'])) {
                            $name = mt_rand() . '_' . $productImage['image']->getClientOriginalName();
                            $path = '/storage/' . $productImage['image']->storeAs(
                                'product_options',
                                $name,
                                'public'
                            );
                        }

                        // $isCombination = ($productOption->color_name === "combo") ? 1 : $productImage['is_combination'];

                        $currentMaxOrder++;
                        $productOption->productImages()->create([
                            'product_option_id' => $productOption->id,
                            'product_id' => $productOption->product_id,
                            'alt_text' => $productImage['alt_text'],
                            'is_combination' => $productImage['is_combination'],
                            'image' => $path,
                            'order' => $currentMaxOrder,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'success',
                'updated_product_options' => ProductOption::with('productImages')->where('product_id', $productId)->get()

            ]);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
