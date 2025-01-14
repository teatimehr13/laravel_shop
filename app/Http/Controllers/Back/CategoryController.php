<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\CategoryRequest;
use App\Http\Resources\Back\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //返回多個模型要用collection
        // $categories = Category::with('subcategories')->orderBy('order_index', 'ASC')->get();
        // // $categories = Category::category_asc()->get();
        // // $categories = Category::orderBy('order_index', 'ASC')->get();
        // return CategoryResource::collection($categories);

        //只會返回一個模型
        // return new CategoryResource($categories);

        $categories = $this->fetchData($request);

        // // 判斷是否為 API 請求
        if ($request->wantsJson()) {
            return response()->json($categories);
        }
        // Log::info($categories);

        return Inertia::render('Back/Category', [
            'category' => $categories
        ]);
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
        $validated = $request->validated();

        $maxOrderIndex = Category::max('order_index');
        $validated['order_index'] = ($maxOrderIndex ?? 0) + 1;

        $category = Category::create($validated);
        return response()->json(['msg' => 'create successful', 'data' => $category]);
        // return new CategoryResource($category);
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
        // Log::info($id);
        // Log::info($request->validated());
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        /**
         * 當類別的子類別存在時，不可刪除該類別
         */
        if ($category->subcategories()->count() !== 0) {
            return response()->json(['message' => 'subcategory exist']);
        }

        if (!$category->delete()) {
            return response()->json(['message' => 'Failed to delete category'], 500);
        }

        return response()->json(['message' => 'success'], 200);
    }

    private function fetchData(Request $request)
    {
        $name = $request->input('name');

        // $query = Store::where('is_enabled', 1);
        // $query = Category::whereIn('show_in_list', [0, 1]);
        $query = Category::with('subcategories') // 加入 Eager Loading
            ->whereIn('show_in_list', [0, 1]);

        if (!is_null($name)) {
            $query->where('name', 'LIKE', "%$name%");
        }

        return $query->paginate(20)->through(fn($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'search_key' => $category->search_key,
            'order_index' => $category->order_index,
            'show_in_list' => $category->show_in_list,
            'subcategories' => $category->subcategories->sortBy('order_index')
                ->values() //更新索引排序
                ->map(fn($sub) => [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'search_key' => $sub->search_key,
                    'order_index' => $sub->order_index,
                    'show_in_list' => $sub->show_in_list,
                    'category_id' => $category->id
                ]),
        ]);
    }
}
