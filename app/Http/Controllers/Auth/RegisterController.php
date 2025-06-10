<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function phoneStep(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^09\d{8}$/'],
            'verify_num' => ['required']
        ]);

        session([
            'register.phone' => $request->phone,
            'register.verify_num' => $request->verify_num,
            'register_data_expired_at' => now()->addMinutes(1)->timestamp,
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
}
