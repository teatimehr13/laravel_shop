<?php

namespace App\Http\Controllers;

use App\Models\ProductOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedIds = $request->input('selected_ids');

        // 拿不到數量
        // $productOptions = ProductOption::with('product')->whereIn('id', $selectedIds)->get();

        // $total = $request->user()->getPurchaseCartOrCreate()->total;

        $checkoutItems = $request->user()
        ->getPurchaseCartOrCreate()
        ->cartItems()
        ->with('productOption.product')
        ->whereIn('product_option_id', $selectedIds)
        ->get()
        ->map(function($item){
            return [
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'productOption' => [
                    'id' => $item->productOption->id,
                    'image' => $item->productOption->image,
                    'price' => $item->productOption->price,
                    'color_name' => $item->productOption->color_name,
                ],
                'product' => [
                    'name' => $item->productOption->product->name,
                    'price' => $item->productOption->product->price,
                ]
            ];
        });

        return Inertia::render('Front/Checkout', [
            'checkoutItems' => $checkoutItems,
            'checkoutTotal' => $checkoutItems->sum('subtotal')
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
