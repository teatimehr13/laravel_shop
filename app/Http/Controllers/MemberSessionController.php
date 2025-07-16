<?php

namespace App\Http\Controllers;

use App\Libraries\MemberAuth;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberSessionController extends Controller
{
    public function create()
    {
        // $member = null;
        // if (session()->exists('memberId')) {
        //     $member = Member::find(session('memberId'));
        // }

        // return view('members.login', [
        //     'member' => $member
        // ]);

        if(MemberAuth::isLoggedIn()){
            return redirect(MemberAuth::HOME);
        }

        return view('members.login');
    }

    public function store(Request $request)
    {

        //在app中新增Libraries資料夾及MemberAuth的檔案自訂funvtion MemberAuth
        MemberAuth::logIn(
            $request->email,
            $request->password
        );

        return redirect(MemberAuth::HOME);
    }

    public function delete(Request $request)
    {
        MemberAuth::logOut();
        return redirect(MemberAuth::HOME);
    }
}
