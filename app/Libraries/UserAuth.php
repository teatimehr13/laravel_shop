<?php

namespace App\Libraries;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Inertia\Inertia;

class UserAuth
{
    private static $user = null;

    public static function login(Request $request)
    {
        $account = $request->input('email');
        $password = $request->input('password');

        // 判斷是 email 還是手機號
        if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $account)->first();
            if (!$user) {
                return back()->withErrors(['email' => '此 Email 尚未註冊']);
            }
            $credentials = ['email' => $account, 'password' => $password];
        } elseif (preg_match('/^09\d{8}$/', $account) || preg_match('/^\+8869\d{8}$/', $account)) {
            $user = User::where('phone', $account)->first();
            if (!$user) {
                return back()->withErrors(['email' => '此手機號碼尚未註冊']);
            }
            $credentials = ['phone' => $account, 'password' => $password];
        } else {
            return back()->withErrors([
                'email' => '請輸入正確的 Email 或手機號碼。',
            ]);
        }

        // //憑證
        // $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $_user = Auth::user();

            // if (!$_user->hasVerifiedEmail()) {
            //     Auth::logout();
            //     return back()->withErrors([
            //         'email' => '請先完成 Email 驗證後再登入。',
            //     ]);
            // }

            self::$user = $_user;
            session(['userId' => Crypt::encryptString(self::$user->id)]);

            //如果有改更密碼的話重新加密
            if (Hash::needsRehash(self::$user->password)) {
                self::$user->password = Hash::make($credentials['password']);
                self::$user->save();
                // Log::info(self::$user->save());
            }

            // Log::info('Session Data:', session()->all());
            // return response()->json(['message' => 'Logged in!'], 200);

            // $redirect = $request->input('redirect', $request->query('redirect'));

            // Log::info($redirect);
            // // 依參數決定跳去哪裡
            // if ($redirect === 'cart') {
            //     return redirect()->route('cart.index');         // /cart
            // }

  
            $redirect = $request->input('redirect');
            if ($redirect === 'cart') {
                return Inertia::location(route('cart.cart'));
            }

            return Inertia::location(route('categories.front.index'));

            // $redirect = $request->input('redirect', 'categories');
            // return redirect()->intended("/$redirect");


        }

        // return response()->json(['message' => 'Invalid credentials'], 401);
        return back()->withErrors([
            'email' => '帳號或密碼輸入錯誤',
        ]);
    }


    public static function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Log::info('Session Data:', session()->all());
        self::$user = null;
        // return response()->json(['message' => 'Logged out!'], 200);
        // return redirect()->route('categories.front.index');
        return Inertia::location(route('categories.front.index'));
    }

    public static function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered!'], 201);
    }

    public static function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent!'], 200)
            : response()->json(['message' => 'Failed to send reset link.'], 400);
    }

    public static function user_data()
    {
        if (empty(self::$user) && session()->exists('userId')) {
            try {
                //解密session，獲取id並拿到該會員資料
                $userId = Crypt::decryptString(session('userId'));
                self::$user = User::find($userId);
            } catch (DecryptException $e) {
            }
        }
        return self::$user;
    }

    public static function isLoggedIn()
    {
        return !empty(self::user_data());
    }
}
