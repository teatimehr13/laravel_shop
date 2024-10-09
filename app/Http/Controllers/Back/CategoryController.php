<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\CategoryRequest;
use App\Http\Resources\Back\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //返回多個模型要用collection
        $categories = Category::with('subcategories')->orderBy('order_index', 'ASC')->get();
        // $categories = Category::category_asc()->get();
        // $categories = Category::orderBy('order_index', 'ASC')->get();
        return CategoryResource::collection($categories);

        //只會返回一個模型
        // return new CategoryResource($categories);
    }

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
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        // return response()->json($category);
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
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
    public function update($id, CategoryRequest $request)
    {
        $category = Category::find($id);
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        /**
         * 當類別的子類別存在時，不可刪除該類別
         */

        if ($category->subcategories()->count() !== 0) {
            return response()->json(['errMsg' => 'Category cant be deleted if subcategory relating']);
        }


        if ($category->delete()) {
            return response()->json(null, 200);
        }

        return response()->json(null, 403);
    }
}
