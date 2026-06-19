<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'superAdmin';
    case Admin      = 'admin';
    case Guest      = 'guest';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => '최고관리자',
            self::Admin      => '관리자',
            self::Guest      => '게스트',
        };
    }
}
