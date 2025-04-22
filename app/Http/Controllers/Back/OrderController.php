<?php


namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Log::info($this->fetchData($request));
        // if ($request->wantsJson()) {
        //     return response()->json($this->fetchData($request));
        // }
        // return response()->json($this->fetchData($request));

        $orders = $this->fetchData($request); // 包含條件篩選 + 分頁

        return Inertia::render('Back/Orders/Index', [
            'orders' => $orders, // 分頁後的訂單資料，Vue 可以直接用
            'filters' => $request->only([
                'order_number', 
                'order_status', 
                'payment_method', 
                'created_at'
            ]), 
        ]);
    }

    public function show(string $id)
    {
        $order_items = Order::with('orderItems')->findOrFail($id);
        return Inertia::render('Back/Orders/Show', [
            'order_items' => $order_items, // 分頁後的訂單資料，Vue 可以直接用
        ]);
    }

    private function fetchData(Request $request)
    {
        $order_number = $request->input('order_number');
        $order_status = $request->input('order_status');
        $payment_method = $request->input('payment_method');
        $created_date = $request->input('created_at');

        $paymentMethodOptions = Order::paymentMethodOptions();

        $query = Order::query()
            ->when($order_number, fn($q) => $q->where('order_number', 'LIKE', "%{$order_number}%"))
            ->when($order_status, fn($q) => $q->where('order_status', $order_status))
            ->when($payment_method, fn($q) => $q->where('payment_method', $payment_method))
            ->when($created_date, fn($q) => $q->whereDate('created_at', $created_date));
            
            $orders = $query->latest()->paginate(10); // 加上 eager loading 與分頁（可自訂）
            $orders->through(function ($order) use ($paymentMethodOptions) {
            // getCollection()->transform // through可替代成getCollection()->transform (ver8.0以下)
            $order->payment_method_label = $paymentMethodOptions[$order->payment_method] ?? '未知';
            $order->order_status_label = $order->order_status_label;
            return $order;
        });
        return $orders;
    }
}
