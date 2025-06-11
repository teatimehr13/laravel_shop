<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsVerificationController extends Controller
{
    public function sendSmsCode(Request $request)
    {
        $phone = $request->input('phone');
        $code = rand(100000, 999999);

        // 存到 session 或資料庫
        session([
            'sms_verify_code' => $code,
            'sms_verify_phone' => $phone,
            'sms_code_expires' => now()->addMinutes(5)
        ]);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);
        $message = "Your verification code is: {$code}";

        $twilio->messages->create($phone, [
            'from' => $from,
            'body' => $message
        ]);

        return response()->json(['success' => true]);
    }
}
