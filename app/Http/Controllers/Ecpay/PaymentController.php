<?php

namespace App\Http\Controllers\Ecpay;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Ecpay\Sdk\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ecpay\Sdk\Response\VerifiedArrayResponse;


class PaymentController extends Controller
{
    // 1) 建立訂單並產生自動送出的 HTML Form
    public function checkout(Request $req)
    {
        $order_number = $req->input('order_number');
        $amount = $req->input('amount');

        $factory = new Factory([
            'hashKey'    => config('services.ecpay.hash_key'),
            'hashIv'     => config('services.ecpay.hash_iv'),
            'merchantId' => config('services.ecpay.merchant_id'),
            'mode'       => 0,
            // 'env'        => config('services.ecpay.env')
        ]);
        // $autoSubmit = $factory->create('AutoSubmitFormService');
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

        $params = [
            // === 必填 ===
            'MerchantID'        => config('services.ecpay.merchant_id'),
            'MerchantTradeNo'   => $order_number, //訂單編號
            'MerchantTradeDate' => now()->format('Y/m/d H:i:s'),
            'PaymentType'       => 'aio',
            'TotalAmount'       => (int) $amount, // 從請求中取得金額
            'TradeDesc'         => urlencode('Laravel 測試訂單'),  //商品描述
            'ItemName'          => '測試商品',  //商品名稱

            // === 回傳網址 ===
            'ReturnURL'      => config('services.ecpay.return_url'), // 用來接收綠界後端回傳的付款結果通知
            'OrderResultURL' => config('services.ecpay.front_url'),  // Client
            // 'NotifyURL'      => config('services.ecpay.notify_url'),

            // === 付款選項 ===
            'ChoosePayment' => 'Credit',
            // 'ChoosePayment'  => 'ALL',   // 讓使用者在綠界頁面自行挑
            'EncryptType'    => 1,
            // 'BuyerEmail' => $request->email,
        ];

        $action = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';
        $html = $autoSubmitFormService->generate($params, $action);
        // if (str_starts_with($html, '<!DOCTYPE')) {
        //     // 找 action
        //     preg_match('/action="([^"]+)"/', $html, $m);
        //     Log::info('Form action 用的 URL', ['action' => $m[1] ?? 'N/A']);
        // }
        return response($html);
    }

    // 2)當消費者付款完成後，特店接受綠界的付款結果訊息，並回應接收訊息
    public function returnUrl(Request $req)
    {

        // Log::info('Hit returnUrl', [
        //     'raw' => $req->all(),
        //     '_POST' => $_POST
        // ]);

        $factory = new Factory([
            'hashKey'    => config('services.ecpay.hash_key'),
            'hashIv'     => config('services.ecpay.hash_iv'),
            'merchantId' => config('services.ecpay.merchant_id'),
        ]);

        $checkoutResponse = $factory->create(VerifiedArrayResponse::class);
        $data = $checkoutResponse->get($req->all());
        Log::info('綠界付款成功資料驗證完成', $data);

        $order = Order::where('payment_token', $data['MerchantTradeNo'])
            ->orWhere('order_number', $data['MerchantTradeNo'])
            ->first();

        $order->update([
            'order_status' => 2,
            'payment_method' => $data['PaymentType'] == 'Credit_CreditCard' ? 'credit_card' : $data['PaymentType'],
            'payment_order_number' => $data['TradeNo'],
            'payment_status' => 'paid',
            // 'paid_at' => now(),
            // 'transaction_id' => $data['TradeNo'],
        ]);

        // return redirect()->route('order.show', ['order' => $data['MerchantTradeNo']]);

        // $data = $req->all();
        // if (!$checker->validate($data)) {     // 驗證失敗
        //     logger()->warning('ECPay callback MAC invalid', $data);
        //     return response('0|FAIL', 200);
        // }

        // //成功後開始進行資料表的處理
        // $order = Order::where('order_no', $data['MerchantTradeNo'])->first();

        // if ($order && $data['RtnCode'] == 1 && $order->amount == $data['TradeAmt']) {
        //     $order->update([
        //         'status' => 'PAID',
        //         'paid_at' => now(),
        //         'ecpay_trade_no' => $data['TradeNo'],
        //     ]);

        //     // //呼叫「自己後台」的 API 儲存 log
        //     // Http::post(route('order.notify'), [
        //     //     'order_id' => $order->id,
        //     //     'event'    => 'payment_success',
        //     //     'from'     => 'ecpay',
        //     //     'data'     => $data,
        //     // ]);
        // }

        return response('1|OK', 200);
    }



    // 導回訂單頁
    public function frontOrderResultURL(Request $req)
    {
        Log::info('OrderResult 回傳內容', $req->all());
        $merchantTradeNo = $req->input('MerchantTradeNo');
        $order = Order::where('order_number', $merchantTradeNo)
            ->orWhere('payment_token', $merchantTradeNo)
            ->firstOrFail();

        // return redirect()->route('order.show', ['order' => $merchantTradeNo])->with('success', '付款完成！訂單已成立');
        return redirect()->route('order.show', ['order' => $order->order_number])->with('success', '付款完成！訂單已成立');
    }

    //v5版本不需要
    public function notifyUrl(Request $req)
    {
        Log::info('收到 NotifyURL 回傳', $req->all());

        $factory = new Factory([
            'hashKey'    => config('services.ecpay.hash_key'),
            'hashIv'     => config('services.ecpay.hash_iv'),
            'merchantId' => config('services.ecpay.merchant_id'),
        ]);

        $verified = $factory->create(VerifiedArrayResponse::class)->get($req->all());

        $order = Order::where('order_number', $verified['MerchantTradeNo'])->first();

        if ($order && $verified['RtnCode'] == '1') {
            $order->update([
                'order_status' => 2,
                'payment_method' => $verified['PaymentType'],
                'payment_order_number' => $verified['TradeNo'],
            ]);
        }

        return response('1|OK');
    }

    public function retry(Order $order)
    {
        // Log::info($order);
        // 確認是該使用者的訂單
        // if ($order->user_id !== auth()->id()) {
        //     abort(403);
        // }


        if ($order->payment_status === 'paid') {
            return redirect()->route('order.show', ['order' => $order->order_number])
                ->with('error', '此訂單無需補繳');
        }


        // $paymentToken = $order->order_number . '-R' . now()->format('His'); //超過20字元
        $paymentToken = 'RE' . $order->id . now()->format('His');
        $order->update(['payment_token' => $paymentToken]);

        return $this->checkout(new Request([
            'order_number' => $paymentToken,
            'amount' => $order->amount
        ]));
    }
}
