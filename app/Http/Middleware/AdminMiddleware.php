<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        $passRoutes       = ['logout'];
        $currentRouteName = request()->route()->getName();

        if ($request->bearerToken() && hash_equals(env('LOG_VIEWER_TOKEN', ''), $request->bearerToken())) {
            return $next($request);
        }

        if ($guard === 'login') {
            if( !in_array($currentRouteName, $passRoutes) && !Auth::guard('admin')->check() ){
                return redirect()->route('login', ['redirectTo' => $request->fullUrl()]);
            }
        }

        if ($guard === 'guest') {
            if (Auth::guard('admin')->check() && in_array($currentRouteName, ['show', 'login'])) {
                return redirect()->route('/')->with('alert', '이미 로그인이 되었습니다.');
            }
        }

        return $next($request);
    }
}
