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
        // if ($request->input('po_id')) {
        //     $productOptionId = $request->input('po_id');

        //     // 根據 productOptionId 獲取相應的產品選項數據
        //     $productImages = ProductOption::find($productOptionId)->productImages()->get();

        //     $formattedProductImages = $productImages->map(function ($image) {
        //         return [
        //             'id' => $image->id,
        //             'alt_text' => $image->alt_text,
        //             'image' => $image->image,
        //             'order' => $image->order,
        //         ];
        //     });

        //     // 返回數據給前端
        //     return response()->json($formattedProductImages);
        // }

        if ($request->input('pr_id')) {
            $productId = $request->input('pr_id');
            // $productId = 2;

            // 根據 productOptionId 獲取相應的產品選項數據
            // $productImages = Product::find($productId)->product_images()->get();
            // $productImages = Product::find($productId)->product_images()->get();
            $productImages = ProductOption::with('productImages')->where('product_id', $productId)->get();

            // $formattedProductImages = $productImages->map(function ($image) {
            //     return [
            //         'id' => $image->id,
            //         // 'alt_text' => $image->alt_text,
            //         'image' => $image->image,
            //         'order' => $image->order,
            //         // 'is_combination' => $image->is_combination,
            //         'product_option_id' => $image->pivot->product_option_id
            //     ];
            // });

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

    // public function updateProductImages(Request $request)
    // {
    //     $validated = $request->validate([
    //         'po_id' => 'required|integer|exists:product_options,id',
    //         'product_images' => 'required|array',
    //         'product_images.*.id' => 'nullable|integer|exists:product_images,id',
    //         'product_images.*.alt_text' => 'nullable|string|max:255',
    //         'product_images.*.is_combination' => 'required|boolean',
    //         'product_images.*.image' => 'nullable|image',
    //         'product_images.*._delete' => 'nullable|boolean',
    //     ]);

    //     Log::info($validated);
    //     return;

    //     // 找到對應的 Product Option
    //     $productOption = ProductOption::findOrFail($validated['po_id']);

    //     // 拿到所有的 product_images 並刪除未被更新的項目
    //     $currentMaxOrder = ProductImage::where('product_id', $productOption->product_id)->max('order') ?? 0;

    //     // product_images 進行新增/更新/刪除
    //     foreach ($validated['product_images'] as $productImage) {
    //         if (!empty($productImage['_delete']) && isset($productImage['id'])) {
    //             // 刪除圖片資料
    //             $existingImage = ProductImage::findOrFail($productImage['id']);
    //             $path = str_replace('/storage/', '', $existingImage->image);
    //             if (Storage::disk('public')->exists($path)) {
    //                 Storage::disk('public')->delete($path);
    //             }
    //             $existingImage->delete();
    //         } elseif (isset($productImage['id'])) {
    //             // 更新圖片資料
    //             $existingImage = ProductImage::findOrFail($productImage['id']);
    //             $existingImage->update([
    //                 'alt_text' => $productImage['alt_text'],
    //                 'is_combination' => $productImage['is_combination'],
    //             ]);

    //             if (isset($productImage['image'])) {
    //                 // 刪除舊圖片並更新新圖片
    //                 $path = str_replace('/storage/', '', $existingImage->image);
    //                 if (Storage::disk('public')->exists($path)) {
    //                     Storage::disk('public')->delete($path);
    //                 }

    //                 $name = mt_rand() . '_' . $productImage['image']->getClientOriginalName();
    //                 $path = '/storage/' . $productImage['image']->storeAs(
    //                     'product_options',
    //                     $name,
    //                     'public'
    //                 );
    //                 $existingImage->update(['image' => $path]);
    //             }
    //         } else {
    //             // 新增圖片
    //             $path = null;
    //             if (isset($productImage['image'])) {
    //                 $name = mt_rand() . '_' . $productImage['image']->getClientOriginalName();
    //                 $path = '/storage/' . $productImage['image']->storeAs(
    //                     'product_options',
    //                     $name,
    //                     'public'
    //                 );
    //             }

    //             $currentMaxOrder++;
    //             $productOption->productImages()->create([
    //                 'product_option_id' => $productOption->id,
    //                 'product_id' => $productOption->product_id,
    //                 'alt_text' => $productImage['alt_text'],
    //                 'is_combination' => $productImage['is_combination'],
    //                 'image' => $path,
    //                 'order' => $currentMaxOrder,
    //             ]);
    //         }
    //     }

    //     return response()->json(['message' => 'Product images updated successfully.']);
    // }


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

            // return;

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

                        // 選擇不更新圖片
                        // if (isset($productImage['image'])) {

                        //     // 更新圖片
                        //     $path = str_replace('/storage/', '', $existingImage->image);
                        //     if (Storage::disk('public')->exists($path)) {
                        //         Storage::disk('public')->delete($path);
                        //     }

                        //     $name = mt_rand() . '_' . $productImage['image']->getClientOriginalName();
                        //     $path = '/storage/' . $productImage['image']->storeAs(
                        //         'product_options',
                        //         $name,
                        //         'public'
                        //     );
                        //     $existingImage->update(['image' => $path]);
                        // }
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
