<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Front/OrderList', $this->getOrderLists());
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
    public function show($order_number)
    {
        return Inertia::render('Front/OrderShow', $this->getOrderData($order_number));
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

    public function fetchOrderData($order_number)
    {
        return response()->json($this->getOrderData($order_number));
    }



    // Controller 裡新增
    private function getOrderData($order_number)
    {
        $order = Order::with('orderItems')->where('order_number', $order_number)->firstOrFail();
        foreach ($order->orderItems as $item) {
            $alreadyReturnQty = ReturnItem::where('order_item_id', $item->id)->sum('quantity');
            $item->available_qty = $item->quantity - $alreadyReturnQty;
        }

        $orderData = $order->toArray();
        $orderData['payment_method_label'] = Order::paymentMethodOptions()[$order->payment_method];
        $orderData['step_index'] = $order->step;
        $orderData['order_status_label'] = $order->order_status_label;
        $orderData['payment_status_label'] = $order->payment_status_label;

        return [
            'order' => $orderData,
            'total_qty' => $order->orderItems->sum('quantity'),
            'return_reasons' => ReturnItem::returnReasonOptions(),
        ];
    }

    private function getOrderLists()
    {
        $user_id = auth()->id();
        $orders = Order::with('orderItems')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        // $orderDatas = $orders->toArray();
        // foreach ($orderDatas as $key => $orderData) {
        //     $orderDatas[$key]['payment_method_label'] = Order::paymentMethodOptions()[$orderData['payment_method']] ?? '未知';
        //     $orderDatas[$key]['step_index'] = Order::orderStatusStepMap()[$orderData['order_status']] ?? 0;
        //     $orderDatas[$key]['total_qty'] = collect($orderData['orderItems'])->sum('quantity');
        // }

        //toArray後就不能再關聯，故用laravel中的transform方式
        $orders->transform(function ($order) {
            $data = $order->toArray();
            unset($data['order_items']);
            return [
                ...$data,
                'payment_method_label' => Order::paymentMethodOptions()[$order->payment_method] ?? '',
                'step_index' => Order::orderStatusStepMap()[$order->order_status] ?? 0,
                'total_qty' => $order->orderItems->sum('quantity'),
            ];
        });
        return ['orders' => $orders];
    }

    public function cancelOrder(Order $order)
    {
        if (!in_array($order->fulfilment_status, ['pending', 'processing', 'shipped'])) {
            return response()->json(['msg' => '此訂單無法取消', 'type' => 'error'], 422);
        }

        $order->fulfilment_status = 'cancelled';

        if ($order->payment_status === 'paid') {
            $order->payment_status = 'refunded';
        } else {
            $order->payment_status = 'pending';
        }

        $order->save();


        return response()->json(['msg' => '訂單已取消', 'type' => 'success']);
    }
}
