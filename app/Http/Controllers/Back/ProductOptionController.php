<?php

namespace App\Http\Controllers;

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

    //取值拿到產品圖片
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

    //增刪改產品選項對應的圖片
    // public function updateProductImages(Request $request)
    // {

    //     $validated = $request->validate([
    //         'po_id' => 'required|integer|exists:product_options,id',
    //         'product_images' => 'required|array',
    //         'product_images.*.alt_text' => 'required|string|max:255',
    //         'product_images.*.is_combination' => 'required|boolean',
    //         'product_images.*.image' => 'nullable|image',
    //     ]);

    //     $productOptionId = $request->input('po_id');
    //     $productOption = ProductOption::find($productOptionId);
    //     $product_images = $productOption->productImages()->get();

    //     // 獲取當前 product_id 下的最大 order 值
    //     $currentMaxOrder = ProductImage::where('product_id', $productOption->product_id)
    //         ->max('order') ?? 0;


    //     //拿到product_options對應的images
    //     $productImagesIdsShouldBeRemove = $productOption->productImages->map(function ($productImage) {
    //         return $productImage->id;
    //     })->toArray();



    //     $product_images = $validated['product_images'];
    //     $new_product_images = []; //給新的dom用的

    //     foreach ($product_images as $id => $product_image) {
    //         //處理有按上傳的圖片
    //         if (isset($product_image['image'])) {
    //             $name = mt_rand() . '_' . $product_image['image']->getClientOriginalName();
    //             $path = '/storage/' . $product_image['image']->storeAs(
    //                 'product_options',
    //                 $name,
    //                 'public',
    //             );
    //             $product_image['image'] = $path;
    //         }

    //         //新的dom走if，原本存在的走else
    //         //product_options[new_1][name]
    //         if (strpos($id, 'new_') !== false) {
    //             // Log::info($product_image);
    //             $product_image['product_id'] = $productOption->product_id;
    //             // 設置 order 為當前最大值加 1
    //             $currentMaxOrder++;
    //             $product_image['order'] = $currentMaxOrder;
    //             $product_image['is_combination'] = 0;
    //             // Log::info($currentMaxOrder);
    //             array_push($new_product_images, new ProductImage($product_image));
    //         } else {
    //             $currentProductImage = ProductImage::where('id', $id)->first();
    //             if ($currentProductImage) {
    //                 if (isset($product_image['image'])) {
    //                     //更新圖片
    //                     $path = str_replace('/storage/', '', $currentProductImage->image);
    //                     if (Storage::disk('public')->exists($path)) {
    //                         Storage::disk('public')->delete($path);
    //                     } else {
    //                         Log::info("Image does not exist.");
    //                     }
    //                 }
    //                 //更新現有的資料
    //                 $currentProductImage->update($product_image);
    //                 //拿到資料庫但沒有被更新的id = 在畫面上被X的id
    //                 $productImagesIdsShouldBeRemove = array_diff(
    //                     $productImagesIdsShouldBeRemove, //原本資料庫中的options id
    //                     [$currentProductImage->id]  //dom節點拿到的 id (被更新的)
    //                 );
    //             }
    //         }
    //     }

    //     //新增新的product_options
    //     // Log::info('123',$product->product_options()->saveMany($new_product_options));
    //     Log::info($new_product_images);
    //     $productOption->productImages()->saveMany($new_product_images);

    //     // Log::info($productImagesIdsShouldBeRemove);
    //     DB::table('product_images')->whereIn('id', $productImagesIdsShouldBeRemove)->delete();
    // }

    public function updateProductImages(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|integer|exists:product_options,id',
            'product_images' => 'required|array',
            'product_images.*.id' => 'nullable|integer|exists:product_images,id',
            'product_images.*.alt_text' => 'required|string|max:255',
            'product_images.*.is_combination' => 'required|boolean',
            'product_images.*.image' => 'nullable|image',
            'product_images.*._delete' => 'nullable|boolean',
        ]);

        // 找到對應的 Product Option
        $productOption = ProductOption::findOrFail($validated['po_id']);

        // 拿到所有的 product_images 並刪除未被更新的項目
        $currentMaxOrder = ProductImage::where('product_id', $productOption->product_id)->max('order') ?? 0;

        // product_images 進行新增/更新/刪除
        foreach ($validated['product_images'] as $productImage) {
            if (!empty($productImage['_delete']) && isset($productImage['id'])) {
                // 刪除圖片資料
                $existingImage = ProductImage::findOrFail($productImage['id']);
                $path = str_replace('/storage/', '', $existingImage->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $existingImage->delete();
            } elseif (isset($productImage['id'])) {
                // 更新圖片資料
                $existingImage = ProductImage::findOrFail($productImage['id']);
                $existingImage->update([
                    'alt_text' => $productImage['alt_text'],
                    'is_combination' => $productImage['is_combination'],
                ]);

                if (isset($productImage['image'])) {
                    // 刪除舊圖片並更新新圖片
                    $path = str_replace('/storage/', '', $existingImage->image);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }

                    $name = mt_rand() . '_' . $productImage['image']->getClientOriginalName();
                    $path = '/storage/' . $productImage['image']->storeAs(
                        'product_options',
                        $name,
                        'public'
                    );
                    $existingImage->update(['image' => $path]);
                }
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

        return response()->json(['message' => 'Product images updated successfully.']);
    }
}
