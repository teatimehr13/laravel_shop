<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'register_data_expired_at' => now()->addMinutes(1)->timestamp
        ]);

        return back(303);
    }

    public function passwordStep(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        session(['register.password' => $request->password]);
        Log::info(session()->all());
        return back(303);
    }

    public function infoStep(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:20', 'regex:/^[\u4e00-\u9fa5A-Za-z\s]+$/'],
            'email' => ['required', 'email']
        ]);

        session([
            'register.name' => $request->name,
            'register.email' => $request->email
        ]);


        $user = User::create([
            'phone' => session('register.phone'),
            'password' => session('register.password'),
            'name' => session('register.name'),
            'email' => session('register.email'),
        ]);

        session()->forget('register');
        return to_route('register.success');
    }
}
