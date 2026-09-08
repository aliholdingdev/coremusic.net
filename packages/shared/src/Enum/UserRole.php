<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Enum;

enum UserRole: string
{
    case USER = 'user';
    case EDITOR = 'editor';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Kullanici',
            self::EDITOR => 'Editor',
            self::ADMIN => 'Admin',
        };
    }
}
