<?php

namespace App\Http\Controllers;

use App\Models\ProductOption;
use App\Models\Order;
use App\Models\OrderItem;
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
        $user = $request->user();

        $checkoutItems = $request->user()
            ->getPurchaseCartOrCreate()
            ->cartItems()
            ->with('productOption.product')
            ->whereIn('product_option_id', $selectedIds)
            ->get()
            ->map(function ($item) {
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
            'checkoutTotal' => $checkoutItems->sum('subtotal'),
            'user' => $user
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

    //結帳
    public function placeOrder(Request $request)
    {
        return $this->createOrderByCart($request);
    }

    //生成訂單
    private function createOrderByCart(Request $request)
    {
        $order_data =  $request->validate([
            'address' => 'required|string',
            'phone' => 'require|string',
            'quantity' => 'required|integer|min:1',
            'id' => 'required|integer|'
        ]);
        return;
        $user_status = $request->user();
        // Log::info($user_status);
        $cart = $user_status->getPurchaseCartOrCreate();
        $amount = $this->getEndPrice($request);

        $order = Order::create([
            'amount' => $amount,
            'address' => 'testing...address',
            'user_id' => $user_status->id
        ]);  

        //利用order找到order items去存儲資料
        $order->orderItems()->saveMany($cart->cartItems->map(function ($cartItem) {
            return new OrderItem([
                'name' => $cartItem->productOption->name,
                'price' => $cartItem->productOption->price,
                'quantity' => $cartItem->quantity,
                'product_option_id' => $cartItem->product_option_id
            ]);
        }));

        //購物車變成訂單後，刪除
        // $cart->cartItems()->delete();
    }
}
