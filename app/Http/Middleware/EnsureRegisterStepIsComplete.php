<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegisterStepIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 設定步驟順序
        $steps = [
            'register.phone' => [],
            'register.password' => ['step1_completed'],
            'register.info' => ['step1_completed', 'step2_completed'],
            // 'register.success' => ['step1_completed', 'step2_completed', 'step3_completed'],
        ];

        $routeName = $request->route()->getName();
        $requiredSteps = $steps[$routeName] ?? null;
        $register = session('register', []);



        //根據網址檢查session
        if ($requiredSteps) {
            foreach ($requiredSteps as $step) {
                // Log::info('middleware debug:', [$step]);
                // Log::info($routeName);
                if (empty($register[$step])) {
                    return redirect()->route('register.phone')->withErrors(['step_message' => '請依序完成註冊流程']);
                }
            }
        }
        return $next($request);
    }
}
