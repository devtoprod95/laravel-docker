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
            $admin = auth('admin')->user();

            // 2. 현재 요청된 라우트의 이름 가져오기 (예: 'admin.dashboard')
            $currentRouteName = $request->route()->getName();

            // 3. 관리자의 모든 역할과 그 역할이 차단한 라우트들을 가져와서 목록 생성
            // flatMap을 사용해 모든 역할의 deniedRoutes를 하나로 합침
            $deniedRoutes = $admin->roles->flatMap->deniedRoutes->pluck('route_name');

            // 4. 현재 라우트 이름이 차단 목록에 포함되어 있는지 확인
            if ($deniedRoutes->contains($currentRouteName)) {
                abort(403, '이 페이지에 접근할 권한이 없습니다.');
            }
        }

        return $next($request);
    }
}
