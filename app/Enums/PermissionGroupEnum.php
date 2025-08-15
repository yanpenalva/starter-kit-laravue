<?php

declare(strict_types = 1);

namespace App\Enums;

enum PermissionGroupEnum: string
{
    case USERS = 'users';
    case ROLES = 'roles';

    public static function label(string $value): string
    {
        return match ($value) {
            self::USERS->value => 'Permissões de Usuários',
            self::ROLES->value => 'Permissões de Perfis',
            default => 'Permissões de ' . ucfirst(str_replace('_', ' ', $value)),
        };
    }
}
