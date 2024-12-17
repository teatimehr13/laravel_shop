<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\StoreRequest;
use App\Models\Store;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpParser\Node\Stmt\TryCatch;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = $this->fetchStores($request);
        
        // 判斷是否為 API 請求
        if ($request->wantsJson()) {
            // sleep(.5); 
            return response()->json($stores);
        }

        return Inertia::render('Back/Store', [
            'stores' => $stores
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
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if ($request->hasFile('image')) {
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = "/storage/" . $request->file('image')->storeAs(
                    'sony/stores',
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $store = Store::create($validated);
            return response()->json($store);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
    public function update(StoreRequest $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function update_stores(StoreRequest $request)
    {
        try {
            $id = $request->input('id');
            $store = Store::find($id);

            $validated = $request->validated();

            // Log::info($request->input('delete_image'));
            // Log::info($request->has('delete_image'));

            //收到delete_image為true時刪掉，為false時不刪 (例如有某照片誤傳，但又沒有適合的圖時)
            if ($request->has('delete_image') && $request->input('delete_image') == true) {
                $path = str_replace('/storage/', '', $store->image);
                Storage::disk('public')->delete($path);
                $validated['image'] = null; // 將 image 字段設為 null
            } else {
                unset($validated['image']);
            }

            if ($request->hasFile('image')) {
                //有新圖片一律把舊的刪掉
                if ($store->image) {
                    $path = str_replace('/storage/', '', $store->image);
                    Storage::disk('public')->delete($path);
                }

                //上傳新圖
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = "/storage/" . $request->file('image')->storeAs(
                    'sony/stores',
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $store->update($validated);

            return response()->json($store);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_stores(Request $request)
    {
        try {
            $id = $request->input('id');
            $store = Store::find($id);
            if (!$store) {
                return response()->json(['error' => 'Store not found'], 404);
            }

            if ($store->image) {
                $path = str_replace('/storage/', '', $store->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $store->delete();
            return response()->json(['success' => true]);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function fetchStores(Request $request)
    {
        $storeType = $request->input('store_type');
        $address = $request->input('address');

        $query = Store::where('is_enabled', 1);

        if (!is_null($storeType)) {
            $query->where('store_type', $storeType);
        }

        if (!is_null($address)) {
            $query->where('address', 'LIKE', "%$address%");
        }

        return $query->paginate(20)->through(fn($store) => [
            'id' => $store->id,
            'store_name' => $store->store_name,
            'address' => $store->address,
            'contact_number' => $store->contact_number,
            'store_type' => $store->store_type,
            'image' => $store->image,
            'opening_hours' => $store->opening_hours,
            'store_type_name' => $store->store_type_name,
        ]);
    }
}
