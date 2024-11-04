<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Libraries\UserAuth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */

    private static $user = null;

    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Log::info($request);
        $request->authenticate();
        // $request->session()->regenerate();

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $_user = Auth::user();
            self::$user = $_user;
            session(['userId' => Crypt::encryptString(self::$user->id)]);
            
            //如果有改更密碼的話重新加密
            if(Hash::needsRehash(self::$user->password)){
                self::$user->password = Hash::make($credentials['password']);
                self::$user->save();
                // Log::info(self::$user->save());
            }

            // Log::info('Session Data:', session()->all());
            return redirect()->intended(RouteServiceProvider::HOME);
            // return response()->json(['message' => 'Logged in!'], 200);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        self::$user = null;
        // return redirect('/');

        return redirect('/login');
        // return redirect()->intended(RouteServiceProvider::LOGOUT);
    }
}
