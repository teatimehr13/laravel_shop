<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ecpay\PaymentController;
use App\Models\ProductOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedIds = $request->input('selected_ids');
        $checkoutItems = $this->getCheckoutItems($user, $selectedIds);
        $shippingFee = $this->calculateShippingFee($checkoutItems->sum('subtotal'));
        $checkoutTotal = $checkoutItems->sum('subtotal') + $shippingFee;

        // 拿不到數量
        // $productOptions = ProductOption::with('product')->whereIn('id', $selectedIds)->get();

        // $total = $request->user()->getPurchaseCartOrCreate()->total;
        // $checkoutInfo = 

        return Inertia::render('Front/Checkout', [
            'checkoutItems' => $checkoutItems,
            'user' => $user,
            'checkoutSummary' => [
                'subtotal' => $checkoutItems->sum('subtotal'),
                'shippingFee' => $shippingFee,
                'total' => $checkoutTotal
            ],
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
    // private function createOrderByCart(Request $request)
    // {
    //     $order_data = $request->validate([
    //         'address' => 'required|string',
    //         'phone' => 'required|string',
    //         'note' => 'nullable|string',
    //         'name' => 'nullable|string',
    //         // 'id' => 'required|integer'
    //     ]);

    //     Log::info($order_data);
    //     return;
    //     $user_status = $request->user();
    //     // Log::info($user_status);
    //     $cart = $user_status->getPurchaseCartOrCreate();
    //     $amount = $this->getEndPrice($request);

    //     $order = Order::create([
    //         'amount' => $amount,
    //         'address' => 'testing...address',
    //         'user_id' => $user_status->id
    //     ]);

    //     //利用order找到order items去存儲資料
    //     $order->orderItems()->saveMany($cart->cartItems->map(function ($cartItem) {
    //         return new OrderItem([
    //             'name' => $cartItem->productOption->name,
    //             'price' => $cartItem->productOption->price,
    //             'quantity' => $cartItem->quantity,
    //             'product_option_id' => $cartItem->product_option_id
    //         ]);
    //     }));

    //     //購物車變成訂單後，刪除
    //     // $cart->cartItems()->delete();
    // }

    private function createOrderByCart(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:product_options,id',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:500',
            'order_status' => 'required|integer',
            'payment_method' => 'required|string'
        ]);

        $user = $request->user();
        $checkoutItems = $this->getCheckoutItems($user, $validated['selected_ids']);

        $cart = $user->getPurchaseCartOrCreate();
        // dd($cart->cartItems()->whereIn('product_option_id',$validated['selected_ids']));
        //刪掉productOption 裡的id

        // return dd($checkoutItems);


        // 總金額（商品總額 + 運費）
        $subtotal = $checkoutItems->sum('subtotal');
        // $shippingFee = 60; // 可改成變數
        $shippingFee = $this->calculateShippingFee($subtotal);
        $total = $subtotal + $shippingFee;

        // 建立 Order
        $order_number = now()->format('YmdHis') . rand(1000, 9999);
        $order = Order::create([
            'user_id' => $user->id,
            'amount' => $total,
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'order_number' => $order_number,
            'order_status' => $validated['order_status'], //變數
            'payment_method' => $validated['payment_method']
        ]);

        // 建立 order_items
        foreach ($checkoutItems as $item) {
            // Log::info($item['productOption']['image']);
            $order->orderItems()->create([
                'name' => $item['product']['name'] . ' (' . $item['productOption']['color_name'] . ')',
                'price' => $item['productOption']['price'],
                'quantity' => $item['quantity'],
                'image' => $item['productOption']['image'],
                'product_option_id' => $item['productOption']['id'],
            ]);
        }

        // 購物車變成訂單後，刪除
        $cart->cartItems()->whereIn('product_option_id',$validated['selected_ids'])->delete();

        
        session()->flash('latest_order_number', $order->order_number);
        // return redirect()->route('order.show', ['order' => $order_number]);
        //把參數導到paymentcontroller 
        return app(PaymentController::class)->checkout(new Request([
            'order_number' => $order->order_number,
            'amount' => $order->amount
        ]));
    }

    //拿到購物車全部的金額
    private function getEndPrice(Request $request)
    {
        return array_reduce(
            $this->getCartItems($request),
            function ($currentValue, $cartItemObj) {
                $productOption = $cartItemObj["productOption"];
                $quantity = $cartItemObj["quantity"];
                return $currentValue + intval($quantity) * $productOption->price ?? 0;
            },
            0
        );
    }

    private function getCheckoutItems(User $user, array $selectedIds)
    {
        return $user->getPurchaseCartOrCreate()
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
    }

    private function calculateShippingFee($subtotal)
    {
        return $subtotal >= 490 ? 0 : 100;
    }
}
