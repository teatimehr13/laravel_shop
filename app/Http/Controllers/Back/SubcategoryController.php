<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\SubcategoryRequest;
use App\Http\Resources\Back\SubcategoryResource;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;


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
    public function store($category_id, SubcategoryRequest $request) {
        $category = Category::find($category_id);
        $subcategory = $category->subcategories()->create($request->validated());
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
    public function update($category_id, $subcategory_id, SubcategoryRequest $request) {
        // $test = [$category_id, $subcategory_id];
        // return response()->json($test);

        $category = Category::find($category_id);
        $subcategory = $category->subcategories()->find($subcategory_id);
        if($subcategory->update($request->validated())){
            return new SubcategoryResource($subcategory);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($category_id, $subcategory_id) {
        $category = Category::find($category_id);
        $subcategory = $category->subcategories()->find($subcategory_id);
        
        if($subcategory->delete()){
            return response()->json(['Msg' => 'delete successful']);
        };
        // return new SubcategoryResource($subcategory);
        // if($subcategory->products()->count() !== 0){
        //     return response()->json(['errMsg' => 'Subcategory cant be deleted if product relating']);
        // }
    }
}
