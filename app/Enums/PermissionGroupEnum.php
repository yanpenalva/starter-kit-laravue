<?php

declare(strict_types = 1);

namespace App\Enums;

enum PermissionGroupEnum: string {
    case USERS = 'users';
    case ROLES = 'roles';

    case ACTIVITY_LOGS = 'activity_logs';

    public static function label(string $value): string {
        return match ($value) {
            self::USERS->value => 'Permissões de Usuários',
            self::ROLES->value => 'Permissões de Perfis',
            self::ACTIVITY_LOGS->value => 'Permissões de Logs',
            default => 'Permissões de ' . ucfirst(str_replace('_', ' ', $value)),
        };
    }
}
