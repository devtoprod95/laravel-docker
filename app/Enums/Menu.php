<?php

namespace App\Enums;

enum Menu: string
{
    case Home      = 'home';
    case Dashboard = 'dashboard';
    case Manage    = 'manage';
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
            self::Manage => [
                'name' => '관리자', 'icon' => 'user-cog', 'route' => 'manage.index', 'pattern' => 'manage*'
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
