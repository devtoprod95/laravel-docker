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
            $admin            = auth('admin')->user();
            $currentRouteName = $request->route()->getName();

            // roles와 deniedRoutes를 한 번에 eager load → 쿼리 2개 (roles 1 + deniedRoutes 1)
            $admin->loadMissing('roles.deniedRoutes');

            $deniedRoutes = $admin->roles->flatMap->deniedRoutes->pluck('route');

            if ($deniedRoutes->contains($currentRouteName)) {
                abort(403, '이 페이지에 접근할 권한이 없습니다.');
            }
        }

        return $next($request);
    }
}
