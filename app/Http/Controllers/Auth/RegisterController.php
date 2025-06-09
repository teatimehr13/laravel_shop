<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
        Log::info(session()->all());
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


        // $user = User::create([
        //     'phone' => session('register.phone'),
        //     'password' => session('register.password'),
        //     'name' => session('register.name'),
        //     'email' => session('register.email'),
        // ]);

        session(['register_success_flag' => true]);
        session()->forget('register');
        // return back(303);
        return to_route('register.success');
    }

    public function showSuccess()
    {
        // 只要 flag 不存在，就不讓進入
        if (!session('register_success_flag')) {
            return redirect()->route('register.phone')
                ->withErrors(['step_message' => '請依序完成註冊流程']);
        }

        session()->forget('register_success_flag');

        return Inertia::render('Auth/RegisterForm/RegisterSuccess');
    }
}
