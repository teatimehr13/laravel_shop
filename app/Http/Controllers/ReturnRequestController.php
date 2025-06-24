<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\Front\ReturnRequest as ReturnRequestForm;
use App\Models\ReturnItem;
use Exception;
use Illuminate\Support\Facades\Log;

class ReturnRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(ReturnRequestForm $request)
    {


        $validated = $request->validated();
        $order = Order::findOrFail($validated['order_id']);
        Log::info($order);

        $order->update([
            'fulfilment_status' => 'returned',
            'payment_status' => 'refunded'
        ]);

        return;
        $totalSubtotal = 0;
        $totalDeduct = 0;
        $totalFinalRefund = 0;

        $pendingReturnItems = [];
        // Log::info($validated);

        //先處理returnItem 但不建
        foreach ($validated['items'] as $item) {
            $orderItem = OrderItem::findOrFail($item['order_item_id']);

            if (!$orderItem) {
                return response()->json(['error' => '找不到商品項目'], 422);
            }

            $alreadyReturn = ReturnItem::where('order_item_id', $orderItem->id)->sum('quantity');
            $availableQty = $orderItem->quantity - $alreadyReturn;

            if ($item['quantity'] > $availableQty) {
                return response()->json([
                    'error' => "退貨數量超過上限（最多 $availableQty 件）"
                ], 422);
            }

            $unit_price = $orderItem->price;
            $name = $orderItem->name;
            $quantity = $item['quantity']; //要退的數量
            $subtotal = $unit_price * $quantity;
            $deduct = 0;  //每件退貨可能扣除的費用
            $finalRefund = $subtotal - $deduct;

            $totalSubtotal += $subtotal;
            $totalDeduct += $deduct;
            $totalFinalRefund += $finalRefund;

            $pendingReturnItems[] = [
                'order_item_id' => $orderItem->id,
                'name' => $name,
                'unit_price' => $unit_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'deduct' => $deduct,
                'final_refund' => $finalRefund,
                'reason' => $item['reason'],
                'description' => $item['description'] ?? null
            ];
        }

        // Log::info($pendingReturnItems);

        try {
            //建完return再建returnItem
            $return = ReturnRequest::create([
                'order_id' => $validated['order_id'],
                'return_number' => Str::uuid(),
                'total_subtotal' => $totalSubtotal,
                'total_deduct_amount' => $totalDeduct,
                'total_refund_amount' => $totalFinalRefund,
                'status' => 'pending',
                'refund_method' => '',
            ]);


            foreach ($pendingReturnItems as $returnItem) {
                $returnItem['return_id'] = $return->id;
                ReturnItem::create($returnItem);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => '建立退貨資料時發生錯誤',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'msg' => '退貨申請已送出',
            // 'return_id' => $return->id,
            // 'return_number' => $return->return_number
        ], 201);
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

    public function fetch_return_history($orderId)
    {
        $returnRequests = ReturnRequest::with(['returnItems.orderItem'])
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($returnRequests);
    }
}
