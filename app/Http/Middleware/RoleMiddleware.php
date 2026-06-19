<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check()) {
            /** @var \App\Models\Admin $admin */
            $admin = auth('admin')->user();
            $currentRouteName = $request->route()->getName();

            // 관리자의 ID를 포함한 고유 캐시 키 생성
            $cacheKey = "admin_denied_routes_{$admin->id}";

            // 캐시에서 가져오거나, 없으면 1시간(3600초) 동안 저장
            $deniedRoutes = cache()->remember($cacheKey, 3600, function () use ($admin) {
                $admin->loadMissing('roles.deniedRoutes');
                return $admin->roles->flatMap->deniedRoutes->pluck('route')->toArray();
            });

            if (in_array($currentRouteName, $deniedRoutes)) {
                abort(403, '이 페이지에 접근할 권한이 없습니다.');
            }
        }

        return $next($request);
    }
}
