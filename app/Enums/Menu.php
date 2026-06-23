<?php

namespace App\Enums;

enum Menu: string
{
    case Home      = 'home';
    case Dashboard = 'dashboard';
    case Admin     = 'admin';
    case Settings  = 'settings';

    public function info(): array
    {
        return match($this) {
            self::Home => [
                'name' => '홈', 'icon' => 'home', 'route' => '/', 'pattern' => '/'
            ],
            self::Dashboard => [
                'name' => '대시보드', 'icon' => 'dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard*'
            ],
            self::Admin => [
                'name' => '관리자', 'icon' => 'user-cog', 'pattern' => 'admin*', 'children' => [
                    ['name' => '관리자 목록', 'route' => 'admin.index', 'pattern' => 'admin.index*'],
                    [
                        'name' => '권한 관리', 'pattern' => 'admin.role*', 'children' => [
                            ['name' => '권한 목록', 'route' => 'admin.role.index', 'pattern' => 'admin.role.index*'],
                            ['name' => '페이지 권한 목록', 'route' => 'admin.role.route.index', 'pattern' => 'admin.role.route.index*'],
                        ]
                    ],
                ]
            ],
            self::Settings => [
                'name' => '설정', 'icon' => 'settings', 'pattern' => 'settings*', 'children' => [
                    ['name' => '일반', 'route' => 'settings.general', 'pattern' => 'settings/general*'],
                    ['name' => '보안', 'route' => 'settings.security', 'pattern' => 'settings/security*'],
                    [
                        'name' => '알림', 'pattern' => 'settings/notifications*', 'children' => [
                            ['name' => '이메일', 'route' => 'settings.notifications.email', 'pattern' => 'settings/notifications/email*'],
                            ['name' => 'SMS', 'route' => 'settings.notifications.sms', 'pattern' => 'settings/notifications/sms*'],
                        ]
                    ],
                ]
            ],
        };
    }
}
