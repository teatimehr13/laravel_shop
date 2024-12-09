<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $storeType = $request->input('store_type');
        $address = $request->input('address');

        // 構建查詢
        // $query = Store::query();
        $query = Store::where('is_enabled', 1);

        if (!is_null($storeType)) {
            $query->where('store_type', $storeType);
        }

        if (!is_null($address)) {
            $query->where('address', 'LIKE', "%$address%");
        }

        $stores = $query->paginate(10);
        // $stores = Store::where('is_enabled', 1)->paginate(10);

        Log::info('SQL: ' . $query->toSql());
        Log::info('Bindings: ' . json_encode($query->getBindings()));

        $storesArr = $stores->toArray();

        $data = [
            'stores' => $stores->items(),
            'links' => $stores->linkCollection(),
            'next_page' => $stores->nextPageUrl(),
        ];

        // 判斷是否為 API 請求
        if ($request->wantsJson()) {
            return response()->json($stores);
        }

        return Inertia::render('Back/Store', [
            // 'data' => $data
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
