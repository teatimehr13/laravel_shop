<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\UserAuth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        return UserAuth::login($request);
    }

    public function logout(Request $request)
    {
        return UserAuth::logout($request);
    }
}
