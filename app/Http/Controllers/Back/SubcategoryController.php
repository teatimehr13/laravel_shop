<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\SubcategoryRequest;
use App\Http\Resources\Back\SubcategoryResource;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($category_id, SubcategoryRequest $request)
    {
        // Log::info($category_id);
        // Log::info($request->validated());
        // return

        $validated = $request->validated();
        $category = Category::find($category_id);

        //找到該類別的子類別的排序最大數值
        $maxOrderIndex = $category->subcategories()->max('order_index');
        $validated['order_index'] = ($maxOrderIndex ?? 0) + 1;

        $subcategory = $category->subcategories()->create($validated);
        return new SubcategoryResource($subcategory);
    }

    public function show(string $id)
    {
        //
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
    //因為有傳入Product實例會自動解析id，故更新無需傳id進來
    public function update($category_id, $subcategory_id, SubcategoryRequest $request)
    {
        // $test = [$category_id, $subcategory_id];
        // return response()->json($test);

        $category = Category::find($category_id);
        $subcategory = $category->subcategories()->find($subcategory_id);
        if ($subcategory->update($request->validated())) {
            return new SubcategoryResource($subcategory);
        }
    }

    public function updateSub($subcategory_id, SubcategoryRequest $request)
    {
        $category_id = $request->input('category_id');
        // $subcategory_id = $request->input('subcategory_id');

        $category = Category::find($category_id);
        $subcategory = $category->subcategories()->find($subcategory_id);
        if ($subcategory->update($request->validated())) {
            return new SubcategoryResource($subcategory);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($category_id, $subcategory_id) {
    //     $category = Category::find($category_id);
    //     $subcategory = $category->subcategories()->find($subcategory_id);

    //     if($subcategory->delete()){
    //         return response()->json(['Msg' => 'delete successful']);
    //     };
    //     // return new SubcategoryResource($subcategory);
    //     // if($subcategory->products()->count() !== 0){
    //     //     return response()->json(['errMsg' => 'Subcategory cant be deleted if product relating']);
    //     // }
    // }


    public function destroy($subcategory_id)
    {
        $subcategory = Subcategory::find($subcategory_id);

        if (!$subcategory) {
            return response()->json(['message' => 'failed'], 404);
        }

        if ($subcategory->delete()) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'failed'], 500);
        }
    }

    public function reorderSubcategories(Request $request)
    {
        $data = $request->all();
        // Log::info($data);

        DB::transaction(function () use ($data) {
            // 將被影響的記錄的 `order_index` 設為臨時值
            foreach ($data as $item) {
                Subcategory::where('id', $item['id'])->update([
                    'order_index' => $item['order_index'] + 1000,
                ]);
            }

            // 重新設置正確的 `order_index`
            foreach ($data as $item) {
                Subcategory::where('id', $item['id'])->update([
                    'order_index' => $item['order_index'],
                ]);
            }
        });
    }
}
