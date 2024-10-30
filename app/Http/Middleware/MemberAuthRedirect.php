<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Libraries\MemberAuth;

class MemberAuthRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!MemberAuth::isLoggedIn()){
            return redirect()->route('members.session.create');
        }
        return $next($request);
    }
}
