<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\JsonResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // $request->validate([
        //     'email' => 'required|email',
        // ]);

        // // We will send the password reset link to this user. Once we have attempted
        // // to send the link, we will examine the response then see the message we
        // // need to show to the user. Finally, we'll send out a proper response.
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        // if ($status == Password::RESET_LINK_SENT) {
        //     return back()->with('status', __($status));
        // }

        // throw ValidationException::withMessages([
        //     'email' => [trans($status)],
        // ]);


        $request->validate([
            'email' => ['required', 'email'],
        ]);

        //新加的自定義限制
        $email = $request->input('email');
        $keyCooldown = 'password-reset-cooldown:' . $email;
        $keyHourly = 'password-reset-hourly:' . $email;
        $keyDaily = 'password-reset-daily:' . $email;

        // RateLimiter::hit($keyCooldown, 10);

        // dd([
        //     'cooldown' => RateLimiter::tooManyAttempts($keyCooldown, 1),
        //     'count' => RateLimiter::attempts($keyCooldown),
        //     'availableIn' => RateLimiter::availableIn($keyCooldown),
        // ]);


        // 1. 是否在 60 秒冷卻中
        if (RateLimiter::tooManyAttempts($keyCooldown, 1)) {
            $seconds = RateLimiter::availableIn($keyCooldown);
            throw ValidationException::withMessages([
                'email' => ["請等待 {$seconds} 秒後再試。"],
            ]);
        }

        // 2. 每小時最多 3 次
        if (RateLimiter::tooManyAttempts($keyHourly, 3)) {
            throw ValidationException::withMessages([
                'email' => ['你已達到每小時寄送上限，請稍後再試。'],
            ]);
        }

        // 3. 每天最多 6 次
        if (RateLimiter::tooManyAttempts($keyDaily, 6)) {
            throw ValidationException::withMessages([
                'email' => ['今日已達寄送上限。'],
            ]);
        }


        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // 成功進行寄送 → 記錄冷卻、時限
        RateLimiter::hit($keyCooldown, 10);           // 冷卻 60 秒
        RateLimiter::hit($keyHourly, 3600);           // 記錄 1 小時
        RateLimiter::hit($keyDaily, 86400);           // 記錄 24 小時

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
            // return response()->json(['status' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
