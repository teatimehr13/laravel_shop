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
            $product = Product::create($validate_data);

            if ($request->hasFile('image')) {
                // unset($validate_data['image']); //刪除驗證的image字段

                $name = time() . '_' . $request->file('image')->getClientOriginalName(); //避免檔名重複
                $path = '/storage/' . $request->file('image')->storeAs(
                    'products/' . $product->id,
                    $name,
                    'public'
                );

                $product->update([
                    'image' => $path
                ]);
            }

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
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return new ProductResource($product);
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
                    'products/' . $product->id,
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

        // 'products/' . $product->id,
        if ($request->hasFile('image')) {
            //有新圖片一律把舊的刪掉
            if ($product_option->image) {
                $path = str_replace('/storage/', '', $product_option->image);
                Storage::disk('public')->delete($path);
            }

            //上傳新圖
            $name = time() . '_' . $request->file('image')->getClientOriginalName();
            $path = "/storage/" . $request->file('image')->storeAs(
                'product_options/' . $product_option->product_id,
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
            $product_option = ProductOption::create($validated);
            Log::info($product_option);

            if ($request->hasFile('image')) {
                // unset($validated['image']); //刪除驗證的image字段

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


    //取值拿到產品圖片
    public function images($id, Request $request)
    {
        // 根據 productOptionId 獲取相應的產品選項數據
        $productImages = ProductOption::with(['productImages' => function ($query) {
            $query->orderBy('order', 'asc');
        }])->where('product_id', $id)->get();

        // 返回數據給前端
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

        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

     public function reorderProductImgs(Request $request)
    {
        $data = $request->input();

        DB::transaction(function () use ($data) {
            // 將被影響的記錄的 `order_index` 設為臨時值
            foreach ($data as $item) {
                ProductImage::where('id', $item['id'])->update([
                    'order' => $item['order'] + 1000,
                ]);
            }

            // 重新設置正確的 `order_index`
            foreach ($data as $item) {
                ProductImage::where('id', $item['id'])->update([
                    'order' => $item['order'],
                ]);
            }
        });
    }
}
