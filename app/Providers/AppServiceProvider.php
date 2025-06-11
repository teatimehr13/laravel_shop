<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => function () {
                return [
                    'user' => auth()->user(),
                ];
            },
            'register' => function () {
                $expiredAt = session('register_data_expired_at');
                // Log::info($expiredAt);
                // Log::info(time());
                if ($expiredAt && time() > $expiredAt) {
                    session()->forget(['register', 'register_data_expired_at']);
                    return ['_expired' => true];
                }

                $register = session('register') ?? [];
                
                if ($register) {
                    $register['_expiredAt'] = $expiredAt; // 把 timestamp 一起傳給前端
                }
                return $register;
            },
            // 'sms' => function () {
            //     $sms_verify_code = session('sms.sms_verify_code') ?? '';
            //     return [
            //         'sms_verify_code' => $sms_verify_code
            //     ];
            // }
        ]);
    }
}
