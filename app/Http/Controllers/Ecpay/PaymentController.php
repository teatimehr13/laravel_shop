<?php

namespace App\Http\Controllers\Ecpay;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Ecpay\Sdk\Factories\Factory;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    // 1) 建立訂單並產生自動送出的 HTML Form
    public function checkout(Request $req)
    {
        $factory = new Factory([
            'hashKey'    => config('services.ecpay.hash_key'),
            'hashIv'     => config('services.ecpay.hash_iv'),
            'merchantId' => config('services.ecpay.merchant_id'),
        ]);
        $autoSubmit = $factory->create('AutoSubmitFormService');

        $params = [
            // === 必填 ===
            'MerchantTradeNo'   => 'LAR' . now()->format('YmdHis') . rand(1000, 9999), //訂單編號
            'MerchantTradeDate' => now()->format('Y/m/d H:i:s'),
            'PaymentType'       => 'aio',
            'TotalAmount'       => (int) $req->input('amount'), // 從請求中取得金額
            'TradeDesc'         => urlencode('Laravel 測試訂單'),  //商品描述
            'ItemName'          => '測試商品',  //商品名稱

            // === 回傳網址 ===
            'ReturnURL'      => config('services.ecpay.return_url'), // 用來接收綠界後端回傳的付款結果通知
            'OrderResultURL' => config('services.ecpay.front_url'),  // Client
            'NotifyURL'      => config('services.ecpay.notify_url'),

            // === 付款選項 ===
            'ChoosePayment'  => 'ALL',   // 讓使用者在綠界頁面自行挑
            'EncryptType'    => 1,
            // 'BuyerEmail' => $request->email,
        ];

        // 送出 HTML (帶 auto-submit script)
        return response($autoSubmit->generate($params));
    }

    // 2)當消費者付款完成後，特店接受綠界的付款結果訊息，並回應接收訊息
    public function returnUrl(Request $req)
    {
        // 驗證 CheckMacValue
        $factory = new Factory([
            'hashKey'    => config('services.ecpay.hash_key'),
            'hashIv'     => config('services.ecpay.hash_iv'),
            'merchantId' => config('services.ecpay.merchant_id'),
        ]);
        $checker = $factory->create('CheckMacValueService');

        $data = $req->all();
        if (!$checker->validate($data)) {     // 驗證失敗
            logger()->warning('ECPay callback MAC invalid', $data);
            return response('0|FAIL', 200);
        }

        //成功後開始進行資料表的處理
        $order = Order::where('order_no', $data['MerchantTradeNo'])->first();

        if ($order && $data['RtnCode'] == 1 && $order->amount == $data['TradeAmt']) {
            $order->update([
                'status' => 'PAID',
                'paid_at' => now(),
                'ecpay_trade_no' => $data['TradeNo'],
            ]);

            // //呼叫「自己後台」的 API 儲存 log
            // Http::post(route('order.notify'), [
            //     'order_id' => $order->id,
            //     'event'    => 'payment_success',
            //     'from'     => 'ecpay',
            //     'data'     => $data,
            // ]);
        }

        return response('1|OK', 200);
    }

    // 3) Browser redirect（非必要但可用來顯示 UI）
    public function front(Request $req)
    {
        return view('ecpay.result', [
            'status'  => $req->input('RtnCode') == 1 ? 'success' : 'fail',
            'message' => urldecode($req->input('RtnMsg', '')),
        ]);
    }

    public function notifyUrl(Request $req)
    {
        // 處理綠界通知
        // 驗證簽名，確保資料未被竄改 (非常重要)
        // ...  (參考綠界文件驗證方式) ...

        // 更新訂單狀態
        // ...

        return 'success'; // 回傳 success 給綠界
    }
}
