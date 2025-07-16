<?php

namespace App\Http\Controllers;

use App\Libraries\MemberAuth;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function create()
    {
        return view('members.register');
    }

    public function store(Request $request)
    {
         $errorMessage = MemberAuth::signUp(
            $request->email,
            $request->password,
            $request->password_confirmation
        );

        if(!empty($errorMessage)){
            return back()->withErrors($errorMessage);
        }

        return redirect('/');
    }
}
