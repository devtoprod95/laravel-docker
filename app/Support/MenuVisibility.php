<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\DeniedRoute;

class MenuVisibility
{
    /**
     * 로그인 여부에 따라 메뉴를 필터링한다.
     *
     * - 비로그인: denied_routes 에 등록된 메뉴만 숨긴다.
     * - 로그인: 현재 관리자의 역할 기준으로 숨길 메뉴를 계산한다.
     */
    public static function filterForAdmin(?Admin $admin, array $menus): array
    {
        return self::filterMenus($menus, self::deniedRoutesFor($admin));
    }

    /**
     * 현재 사용자 기준으로 금지 라우트 목록을 만든다.
     *
     * - 게스트: ['admin.index', 'settings.general']
     * - 로그인 관리자: [1 => ['admin.index'], 2 => ['settings.general']]
     */
    private static function deniedRoutesFor(?Admin $admin): array
    {
        if ($admin === null) {
            return DeniedRoute::query()->pluck('route')->toArray();
        }

        $admin->loadMissing('roles.deniedRoutes');

        $deniedRoutesByRole = [];

        foreach ($admin->roles as $role) {
            $deniedRoutesByRole[$role->id] = $role->deniedRoutes->pluck('route')->toArray();
        }

        return $deniedRoutesByRole;
    }

    /**
     * 메뉴 배열 전체를 순회하면서 보이는 메뉴만 남긴다.
     */
    private static function filterMenus(array $menus, array $deniedRoutes): array
    {
        if (empty($deniedRoutes)) {
            return $menus;
        }

        $visibleMenus = [];

        foreach ($menus as $menu) {
            $visibleMenu = self::filterMenuItem($menu, $deniedRoutes);

            if ($visibleMenu !== null) {
                $visibleMenus[] = $visibleMenu;
            }
        }

        return $visibleMenus;
    }

    /**
     * 메뉴 1개를 재귀적으로 검사한다.
     *
     * - 현재 메뉴가 숨김 대상인지 확인
     * - 자식 메뉴가 있으면 자식도 같은 방식으로 필터링
     * - 자신과 자식이 모두 숨겨지면 null 반환
     */
    private static function filterMenuItem(array $item, array $deniedRoutes): ?array
    {
        $visibleChildren = [];

        if (!empty($item['children']) && is_array($item['children'])) {
            foreach ($item['children'] as $child) {
                $visibleChild = self::filterMenuItem($child, $deniedRoutes);

                if ($visibleChild !== null) {
                    $visibleChildren[] = $visibleChild;
                }
            }
        }

        $routeName = $item['route'] ?? null;
        $isDenied  = $routeName !== null && self::shouldHideRoute((string) $routeName, $deniedRoutes);

        if ($isDenied && empty($visibleChildren)) {
            return null;
        }

        if ($routeName === null && empty($visibleChildren)) {
            return null;
        }

        if (empty($visibleChildren)) {
            unset($item['children']);
        } else {
            $item['children'] = $visibleChildren;
        }

        return $item;
    }

    /**
     * 현재 라우트를 숨길지 판단한다.
     *
     * - 게스트용 목록이면 단순 포함 여부 확인
     * - 역할별 목록이면 모든 역할에서 금지된 경우만 숨김
     */
    private static function shouldHideRoute(string $routeName, array $deniedRoutes): bool
    {
        if ($deniedRoutes !== [] && is_array(reset($deniedRoutes))) {
            return self::isDeniedInAllRoles($routeName, $deniedRoutes);
        }

        return in_array($routeName, $deniedRoutes, true);
    }

    /**
     * 모든 역할에서 같은 라우트를 금지하고 있을 때만 true 를 반환한다.
     */
    private static function isDeniedInAllRoles(string $routeName, array $deniedRoutesByRole): bool
    {
        return collect($deniedRoutesByRole)->every(function (array $deniedRoutes) use ($routeName) {
            return in_array($routeName, $deniedRoutes, true);
        });
    }
}
