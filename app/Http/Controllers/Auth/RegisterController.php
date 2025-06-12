<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class RegisterController extends Controller
{
    public function phoneStep(Request $request)
    {
        // Log::info($request->input('verify_num'));
        // Log::info(session('sms.sms_verify_code'));
        // if ($request->input('verify_num') !== session('sms.sms_verify_code')) {
        //     Log::info(123);
        // }

        $request->validate([
            'phone' => ['required', 'regex:/^09\d{8}$/'],
            'verify_num' => ['required']
        ]);

        //驗證碼有無過期
        if (!session('sms.sms_verify_code') || now()->gt(session('sms.sms_code_expires'))) {
            return back()->withErrors(['verify_num' => '驗證碼無效或已過期，請重新獲取']);
        }

        //手機號碼和session是否一致
        if (session('sms.sms_verify_phone') != $request->input('phone')) {
            return back()->withErrors(['phone' => '手機號碼不一致']);
        }

        // 比對驗證碼
        if ($request->input('verify_num') != session('sms.sms_verify_code')) {
            return back()->withErrors(['verify_num' => '驗證碼錯誤']);
        }


        session([
            'register.phone' => $request->phone,
            'register.verify_num' => $request->verify_num,
            'register_data_expired_at' => now()->addMinutes(30)->timestamp,  //停留在表單30分鐘上導頁
            'register.step1_completed' => true
        ]);

        return back(303);
    }

    public function passwordStep(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        session([
            'register.password' => $request->password,
            'register.step2_completed' => true
        ]);
        // Log::info(session()->all());
        return back(303);
    }

    public function infoStep(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:20', 'regex:/^[\x{4e00}-\x{9fa5}A-Za-z\s]+$/u'],
            'email' => ['required', 'email']
        ]);

        session([
            'register.name' => $request->name,
            'register.email' => $request->email,
            'register.step3_completed' => true
        ]);


        $user = User::create([
            'phone' => session('register.phone'),
            'password' => session('register.password'),
            'name' => session('register.name'),
            'email' => session('register.email'),
        ]);

        session()->forget('register');
        session()->forget('sms');

        $token = Str::random(32);
        session([
            'register_success_token' => $token,
            'register_success_token_expires' => now()->addMinutes(1)->timestamp // 1分鐘
        ]);

        return to_route('register.success');
    }

    public function showSuccess()
    {
        $sessionToken = session('register_success_token');
        $expiresAt = session('register_success_token_expires');
        Log::info(session()->all());
        // Log::info($sessionToken);
        // Log::info($expiresAt);
        // Log::info(time());

        if (!session('register_success_token') || time() > session('register_success_token_expires')) {
            return $sessionToken
                ? redirect()->route('login')
                : redirect()->route('register.phone');
        }

        return Inertia::render('Auth/RegisterForm/RegisterSuccess');
    }

    public function sendSmsCode(Request $request)
    {
        // session()->forget([
        //     'sms.sms_verify_code',
        //     'sms.sms_verify_phone',
        //     'sms.sms_code_expires',
        //     'sms.sms_last_send_at',
        //     'sms.sms_daily_count',
        // ]);
        // session()->forget('sms');
        // return;
        $phone = $request->input('phone');

        $user = User::where('phone', $phone)->first();
        if($user){
            return response()->json(['error' => '此號碼已註冊'], 429);
        }


        $code = rand(100000, 999999);
        if (preg_match('/^09\d{8}$/', $phone)) {
            $phone = '+886' . substr($phone, 1);
        }

        // 限制60秒內不能重複發送
        $lastSend = session('sms.sms_last_send_at');
        if ($lastSend && now()->diffInSeconds($lastSend) < 60) {
            return response()->json(['error' => '請稍後再試'], 429);
        }

        // 限制每天最多5次
        $dailyCount = session('sms.sms_daily_count', 0);
        // if ($dailyCount >= 5) {
        //     return response()->json(['error' => '今日發送次數已達上限'], 429);
        // }

        // // 存到 session 或資料庫
        session([
            'sms.sms_verify_code' => $code,
            'sms.sms_verify_phone' => $request->input('phone'),
            'sms.sms_code_expires' => now()->addMinutes(5),
            'sms.sms_last_send_at' => now(),
            'sms.sms_daily_count' => $dailyCount + 1
        ]);


        Log::info(session()->all());
        // Log::info($phone);

        // 使用twilio寄送至手機
        // $sid = env('TWILIO_SID');
        // $token = env('TWILIO_AUTH_TOKEN');
        // $from = env('TWILIO_PHONE_NUMBER');

        // $twilio = new Client($sid, $token);
        // $message = "您註冊的驗證碼為: {$code}";

        // $twilio->messages->create($phone, [
        //     'from' => $from,
        //     'body' => $message
        // ]);

        return response()->json(['success' => true, 'sms' => $code]);
    }
}
