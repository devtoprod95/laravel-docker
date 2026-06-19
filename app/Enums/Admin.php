<?php

namespace App\Enums;

enum Admin: string
{
    case ACTIVE   = '1';
    case INACTIVE = '0';

    case ListSearchUsername = 'username';
    case ListSearchName     = 'name';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE             => '활성',
            self::INACTIVE           => '정지',
            self::ListSearchUsername => '아아디',
            self::ListSearchName     => '이름',
        };
    }

    public static function isActives(): array
    {
        return [
            self::ACTIVE->value   => self::ACTIVE->label(),
            self::INACTIVE->value => self::INACTIVE->label(),
        ];
    }

    public static function listSearchTypes(): array
    {
         return [
            self::ListSearchUsername->value => self::ListSearchUsername->label(),
            self::ListSearchName->value     => self::ListSearchName->label(),
        ];
    }

    public function toBool(): bool
    {
        return $this === self::ACTIVE;
    }
}
