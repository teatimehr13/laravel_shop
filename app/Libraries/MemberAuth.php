<?php

namespace App\Libraries;

use App\Models\Member;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class MemberAuth
{
    public const HOME = '/';
    private static $member = null;

    public static function member()
    {
        if (empty(self::$member) && session()->exists('memberId')) {
            try {
                //解密session，獲取id並拿到該會員資料
                $memberId = Crypt::decryptString(session('memberId'));
                self::$member = Member::find($memberId);
            }catch (DecryptException $e){

            }
            // self::$member = Member::find(session('memberId'));
        }
        return self::$member;
    }

    public static function isLoggedIn()
    {
        return !empty(self::member());
    }

    public static function logIn($email, $password)
    {
        $_member = Member::where([
            'email' => $email,
        ])->first();

        if (
            !empty($_member) &&
            Hash::check($password, $_member->password)
        ) {
            self::$member = $_member;
            //將session的值進行加密
            session(['memberId' => Crypt::encryptString(self::$member->id)]);
            if (Hash::needsRehash($_member->password)) {
                self::$member->password = Hash::make($password);
                self::$member->save();
            }
        }
    }

    public static function logOut()
    {
        session()->forget('memberId');
        self::$member = null;
    }

    public static function signUp($email, $password, $password_confirmation){
        if($password === $password_confirmation){
            try {
                Member::create([
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
            } catch (QueryException $e) {
                return "Email or password invalid";
            }
            return null;
        }
        return "Password and password confirmation are not compated.";
    }
}
