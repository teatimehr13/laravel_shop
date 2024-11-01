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

class UserAuth
{
    private static $user = null;

    public static function login(Request $request)
    {
        //憑證
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
            return response()->json(['message' => 'Logged in!'], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }


    public static function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('Session Data:', session()->all());
        self::$user = null;
        return response()->json(['message' => 'Logged out!'], 200);
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
            }catch (DecryptException $e){

            }
        }
        return self::$user;
    }

    public static function isLoggedIn()
    {
        return !empty(self::user_data());
    }
}
