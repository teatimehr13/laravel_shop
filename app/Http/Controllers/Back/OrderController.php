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
        $order_status_select = Order::orderStatusSelect();
        $payment_method_select = Order::paymentMethodOptions();


        return Inertia::render('Back/Orders/Index', [
            'orders' => $orders, // 分頁後的訂單資料，Vue 可以直接用
            'order_status_select' =>$order_status_select,
            'payment_method_select' => $payment_method_select,
            'filters' => $request->only([
                'order_number', 
                'order_status', 
                'payment_method', 
                'created_at',
                'sort_by',
                'sort_dir'
            ]), 
        ]);
    }

    public function show(string $id)
    {
        $order_items = Order::with('orderItems')->findOrFail($id);
        $shippingFee = $this->calculateShippingFee($order_items->amount);
        $order_items->shippingFee = $shippingFee;
        $paymentMethodOptions = Order::paymentMethodOptions();
        $order_items->payment_method_label = $paymentMethodOptions[$order_items->payment_method] ?? '未知';
        $order_items->order_status_label = $order_items->order_status_label;
        return Inertia::render('Back/Orders/Show', [
            'order_items' => $order_items, // 分頁後的訂單資料，Vue 可以直接用
            // 'shippingFee' => $shippingFee
        ]);
    }

    private function fetchData(Request $request)
    {
        $order_number = $request->input('order_number');
        $order_status = $request->input('order_status');
        $payment_method = $request->input('payment_method');
        // $payment_method = $request->filled('payment_method') ? (int) $request->input('payment_method') : null;
        $created_date = $request->input('created_at');

        $paymentMethodOptions = Order::paymentMethodOptions();

        $query = Order::query()
            ->when($order_number, fn($q) => $q->where('order_number', 'LIKE', "%{$order_number}%"))
            ->when(!is_null($order_status), fn($q) => $q->where('order_status', $order_status))
            ->when($payment_method, fn($q) => $q->where('payment_method', $payment_method))
            ->when($created_date, fn($q) => $q->whereDate('created_at', $created_date));
            
            $sort_by = $request->input('sort_by', 'created_at'); //預設用時間
            $sort_dir = $request->input('sort_dir', 'desc'); //預設desc

            $validSorts = ['created_at', 'amount'];
            $validDirs = ['asc', 'desc'];

            if (in_array($sort_by, $validSorts) && in_array($sort_dir, $validDirs)) {
                $query->orderBy($sort_by, $sort_dir);
            }

            $orders = $query->latest()->paginate(10); // 加上 eager loading 與分頁（可自訂

            $orders->through(function ($order) use ($paymentMethodOptions) {
            // getCollection()->transform // through可替代成getCollection()->transform (ver8.0以下)
            $order->payment_method_label = $paymentMethodOptions[$order->payment_method] ?? '未知';
            $order->order_status_label = $order->order_status_label;
            return $order;
        });
        return $orders;
    }

    private function calculateShippingFee($subtotal)
    {
        return $subtotal >= 490 ? 0 : 100;
    }
}
