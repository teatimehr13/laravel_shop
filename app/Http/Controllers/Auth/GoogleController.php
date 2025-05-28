<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        // dd($googleUser);

        // $user = User::where('email', $googleUser->getEmail())->first();

        // if (!$user) {
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(str()->random(16)),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'phone' => '',
            ]
        );
        // }

        Auth::login($user);

        return to_route('categories.front.index');
    }
}
