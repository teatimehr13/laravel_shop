<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductRequest;
use App\Http\Resources\Back\ProductResource;
use App\Models\Category;
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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'subcategory.category.subcategories' => function ($query) {
                $query->orderBy('order_index', 'asc');
            },
            'product_options',
        ])->get();

        $categories = Category::where('show_in_list', 1)->get(['id', 'name']);

        $formattedProducts = $products->map(function ($product) use ($categories) {
            $productArray = $product->toArray();
            // Log::info($productArray);

            // 提取產品選項的顏色
            $productArray['color_codes'] = $product->product_options->pluck('color_name');
            $productArray['subcategory'] = $product->subcategory
                ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->name,
                ]
                : null;

            // 如果有子類別，加入子類別的詳細資料
            if ($product->subcategory && $product->subcategory->category) {
                $productArray['subcategories'] = $product->subcategory->category->subcategories->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        // 'category' => $sub->category
                        // 'order_index' => $sub->order_index,
                        // 'show_in_list' => $sub->show_in_list,
                    ];
                });
            } else {
                $productArray['subcategories'] = [];
            }


            unset($productArray['product_options']);
            return $productArray;
        });

        return Inertia::render('Back/Product', [
            'products' => $formattedProducts,
            'categories' => $categories
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
        //
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
            
            if(!$product->delete()){
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

    public function prod_options($product_id){
        $product = Product::find($product_id);
        $product_option = $product->product_options()->get();
        return response()->json($product_option);
    }
}
