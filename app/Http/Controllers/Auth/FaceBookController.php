<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class FaceBookController extends Controller
{
     public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();
        // dd($facebookUser);

        $user = User::updateOrCreate(
            ['email' => $facebookUser->getEmail()],
            [
                'name' => $facebookUser->getName(),
                'avatar' => $facebookUser->getAvatar(),
                'password' => bcrypt(str()->random(16)),
                'provider' => 'facebook',
                'provider_id' => $facebookUser->getId(),
                'phone' => '0',
            ]
        );

        Auth::login($user);

        return to_route('categories.front.index');
    }
}
