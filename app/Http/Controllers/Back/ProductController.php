<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductRequest;
use App\Http\Requests\Back\ProductOptionRequest;
use App\Http\Resources\Back\ProductResource;
use App\Models\Category;
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
use PDO;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $products = Product::with([
        //     'subcategory.category.subcategories' => function ($query) {
        //         $query->orderBy('order_index', 'asc');
        //     },
        //     'product_options',
        // ])->get();

        // $categories = Category::where('show_in_list', 1)->get(['id', 'name']);

        // $formattedProducts = $products->map(function ($product) use ($categories) {
        //     $productArray = $product->toArray();
        //     // Log::info($productArray);

        //     // 提取產品選項的顏色
        //     $productArray['color_codes'] = $product->product_options->pluck('color_name');
        //     $productArray['subcategory'] = $product->subcategory
        //         ? [
        //             'id' => $product->subcategory->id,
        //             'name' => $product->subcategory->name,
        //         ]
        //         : null;

        //     // 如果有子類別，加入子類別的詳細資料
        //     if ($product->subcategory && $product->subcategory->category) {
        //         $productArray['subcategories'] = $product->subcategory->category->subcategories->map(function ($sub) {
        //             return [
        //                 'id' => $sub->id,
        //                 'name' => $sub->name,
        //                 // 'category' => $sub->category
        //                 // 'order_index' => $sub->order_index,
        //                 // 'show_in_list' => $sub->show_in_list,
        //             ];
        //         });
        //     } else {
        //         $productArray['subcategories'] = [];
        //     }


        //     unset($productArray['product_options']);
        //     return $productArray;
        // });

        // return Inertia::render('Back/Product', [
        //     'products' => $formattedProducts,
        //     'categories' => $categories
        // ]);
        // $products
        // $categories
        // 'filters'

        ['products' => $products, 'categories' => $categories, 'filters' => $filters, 'subcategories' => $subcategories] = $this->fetchData($request);

        return Inertia::render('Back/Product', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'subcategories' => $subcategories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProductRequest $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        try {
            $validate_data = $request->validated();
            Log::info($validate_data);
            // return;
            // return response()->json($validate_data);

            if ($request->hasFile('image')) {
                // unset($validate_data['image']); //刪除驗證的image字段

                $name = time() . '_' . $request->file('image')->getClientOriginalName(); //避免檔名重複
                $path = '/storage/' . $request->file('image')->storeAs(
                    'products',
                    $name,
                    'public'
                );
                $validate_data["image"] = $path;
            }

            $product = Product::create($validate_data);
            return response()->json($product, 201); // 回傳成功創建的產品資料
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        // $product = Product::with(['product_options'])->find($id);
        // Product::with(['subcategory:id,name', 'product_options'])->get();
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return new ProductResource($product);
        // return response()->json($this->updateProductOptions($product));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::find($id);
        $subcategories = $product->subcategory->category->subcategories;

        return Inertia::render('Back/Product/Edit', [
            'product' => $product,
            'subcategories' => $subcategories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ProductRequest $request)
    {
        try {
            $product = Product::find($id);
            $validated = $request->validated();

            // Log::info($product);
            // Log::info($validate_data);

            //收到delete_image為true時刪掉，為false時不刪 (例如有某照片誤傳，但又沒有適合的圖時)
            if ($request->has('delete_image') && $request->input('delete_image') == true) {
                $path = str_replace('/storage/', '', $product->image);
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
                        $product->image
                    )
                );
            }


            if ($request->hasFile('image')) {
                //有新圖片一律把舊的刪掉
                if ($product->image) {
                    $path = str_replace('/storage/', '', $product->image);
                    Storage::disk('public')->delete($path);
                }

                //上傳新圖
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = "/storage/" . $request->file('image')->storeAs(
                    'products',
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $product->update($validated);

            // 重新載入關聯資料
            $product->load([
                'subcategory.category.subcategories' => function ($query) {
                    $query->orderBy('order_index', 'asc');
                },
                'product_options',
            ]);


            // 格式化資料
            $formattedProduct = $product->toArray();

            // 提取產品選項的顏色
            $formattedProduct['color_codes'] = $product->product_options->pluck('color_code');

            // 加入子類別資訊
            $formattedProduct['subcategory'] = $product->subcategory
                ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->name,
                ]
                : null;

            // 加入子類別的詳細資料
            if ($product->subcategory && $product->subcategory->category) {
                $formattedProduct['subcategories'] = $product->subcategory->category->subcategories->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                    ];
                });
            } else {
                $formattedProduct['subcategories'] = [];
            }

            // 回傳格式化後的資料
            return response()->json($formattedProduct, 200);
        } catch (QueryException $e) {
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            $image = $product->image ? str_replace('/storage/', '', $product->image) : '';
            if ($image && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }

            if (!$product->delete()) {
                return response()->json(['message' => 'failed'], 200);
            }

            return response()->json(['message' => 'success'], 200);
        }

        return response()->json(null, 403);
    }

    private function updateProductOptions($product, $validate_data)
    {
        // Log::info($product);
        //拿到該product所關聯的所有product_options的id
        //將collection轉成php array
        $productOptionsIdsShouldBeRemove = $product->product_options->map(function ($product_option) {
            return $product_option->id;
        })->toArray();
        // return $productOptionsIds;
        // Log::info($productOptionsIdsShouldBeRemove);

        //$validate_data['product_options']會拿到 product_options[1][name]...等 
        // Log::info($validate_data['product_options']);
        if (isset($validate_data['product_options'])) {
            $product_options_data = $validate_data['product_options'];
            $new_product_options = [];
            // Log::info('151', $product_options_data);

            foreach ($product_options_data as $id => $product_option_data) {
                $validator = Validator::make($product_option_data, [
                    'name' => 'required|string|max:255',
                    'price' => 'required|integer|min:0',
                    'enable' => 'nullable',
                    'image' => 'nullable|image',
                    'description' => 'nullable|string',
                    'published_status' => 'integer'
                ]);


                if (!$validator->fails()) {

                    //驗證沒問題時先檢查圖片
                    if (isset($product_option_data['image'])) {
                        //隨機數字
                        $name = mt_rand() . '_' . $product_option_data['image']->getClientOriginalName();

                        //這個地方沒有用$request 所以使用Storage::disk()的寫法
                        $path = '/storage/' . Storage::disk('public')->putFileAs(
                            'product_options',
                            $product_option_data['image'],
                            $name
                        );
                        $product_option_data['image'] = $path;
                    }

                    //新的dom走if，原本存在的走else
                    //product_options[new_1][name]
                    if (strpos($id, 'new_') !== false) {
                        // Log::info(123);
                        // Log::info($product_option_data);
                        //必需將$product_option_data實例化才能使用eloquent模型的功能
                        array_push($new_product_options, new ProductOption($product_option_data));
                    } else {
                        // $currentProductOption = ProductOption::where(
                        //     [
                        //         ['id', $id],
                        //         ['product_id', $product->id],
                        //     ]
                        // )->first();
                        $currentProductOption = ProductOption::where('id', $id)->where('product_id', $product->id)->first();

                        if ($currentProductOption) {
                            //更新圖片
                            if (isset($product_option_data['image'])) {
                                Storage::disk('public')->delete(
                                    '/storage/',
                                    '',
                                    $currentProductOption->image
                                );
                            }

                            //更新現有的資料
                            $currentProductOption->update($product_option_data);

                            //拿到資料庫但沒有被更新的id = 在畫面上被X的id
                            $productOptionsIdsShouldBeRemove = array_diff(
                                $productOptionsIdsShouldBeRemove, //原本資料庫中的options id
                                [$currentProductOption->id]  //dom節點拿到的 id (被更新的)
                            );
                        }
                    }
                }
            }

            //新增新的product_options
            Log::info($new_product_options);
            // Log::info($product->product_options()->saveMany($new_product_options));
            // Log::info('123',$product->product_options()->saveMany($new_product_options));
            $product->product_options()->saveMany($new_product_options);
        }
        DB::table('product_options')->whereIn('id', $productOptionsIdsShouldBeRemove)->delete();
    }

    private function fetchData(Request $request)
    {
        $p_name = $request->query('name');
        $subcategory_id = $request->query('subcategory_id');

        $query = Product::with([
            'subcategory.category.subcategories' => function ($query) {
                $query->orderBy('order_index', 'asc');
            },
            'product_options'
        ]);

        if (!is_null($p_name)) {
            $query->where('name', 'LIKE', "%{$p_name}%");
        }

        if (!is_null($subcategory_id)) {
            $query->where('subcategory_id', '=', "$subcategory_id");
        }

        $products = $query->paginate(10)->withQueryString(); // 保留 query string 分頁切換不掉查詢

        // 格式化資料
        $formattedProducts = $products->getCollection()->map(function ($product) {  //有用到paginate所以要加getCollection()
            $productArray = $product->toArray();

            $productArray['color_codes'] = $product->product_options->pluck('color_name');
            $productArray['subcategory'] = $product->subcategory
                ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->name,
                ]
                : null;

            $productArray['subcategories'] = $product->subcategory && $product->subcategory->category
                ? $product->subcategory->category->subcategories->map(fn($sub) => [
                    'id' => $sub->id,
                    'name' => $sub->name,
                ])
                : [];

            unset($productArray['product_options']);
            return $productArray;
        });

        $products->setCollection($formattedProducts);

        $categories = Category::where('show_in_list', 1)->get(['id', 'name']);
        $subcategories = Subcategory::select('id', 'name')->get();

        return [
            'products' => $products,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'filters' => [
                'name' => $p_name,
                'subcategory_id' => $subcategory_id,
            ],
        ];
        
    }


    public function getSubSel($category_id)
    {
        $category = Category::with('subcategories')->find($category_id);

        if (!$category || !$category->subcategories) {
            return response()->json(['subSel' => []]);
        }

        $subSel = $category->subcategories->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
            ];
        });

        return response()->json($subSel);
    }

    public function prod_options($product_id)
    {
        $product = Product::find($product_id);
        $product_option = $product->product_options()->get();
        return response()->json($product_option);
    }

    public function updateProdCo($product_option_id, ProductOptionRequest $request)
    {
        $product_option = ProductOption::find($product_option_id);
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
                'product_options',
                $name,
                'public'
            );
            $validated['image'] = $path;
        }

        $product_option->update($validated);

        return response()->json($product_option);
    }

    public function addProdCo(ProductOptionRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['published_status'] = 1;
            // return response()->json($validated);

            if ($request->hasFile('image')) {
                // unset($validated['image']); //刪除驗證的image字段

                $name = time() . '_' . $request->file('image')->getClientOriginalName(); //避免檔名重複
                $path = '/storage/' . $request->file('image')->storeAs(
                    'product_options',
                    $name,
                    'public'
                );
                $validated["image"] = $path;
            }

            $product_option = ProductOption::create($validated);
            return response()->json($product_option, 201); // 回傳成功創建的產品資料
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        };
    }


    //取值拿到產品圖片
    public function images($id, Request $request)
    {
        // 根據 productOptionId 獲取相應的產品選項數據
        $productImages = ProductOption::with('productImages')->where('product_id', $id)->get();

        // 返回數據給前端
        // return response()->json($productImages);
        return Inertia::render('Back/Product/Images', [
            'productImages' => $productImages,
            'productId' => $id
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

            // ajax的方法
            return response()->json([
                'message' => 'success',
                'updated_product_options' => ProductOption::with('productImages')->where('product_id', $productId)->get()

            ]);


            // return Inertia::render('Back/Product/Images', [
            //     'productImages' => ProductOption::with('productImages')->where('product_id', $productId)->get(),
            //     'productId' => $productId,
            // ]);

            // return response()->noContent(); // 204 No Content
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
