<?php

declare(strict_types = 1);

namespace App\Enums;

enum PermissionEnum: string {
    case USERS_LIST = 'users.list';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';
    case USERS_SHOW = 'users.show';

    case ROLES_LIST = 'roles.list';
    case ROLES_VIEW = 'roles.view';
    case ROLES_CREATE = 'roles.create';
    case ROLES_EDIT = 'roles.edit';
    case ROLES_DELETE = 'roles.delete';

    case ACTIVITY_LOGS_LIST = 'activity_logs.list';

    public function description(): string {
        return match ($this) {
            self::USERS_LIST => 'Listar usuários',
            self::USERS_CREATE => 'Criar usuários',
            self::USERS_UPDATE => 'Editar usuários',
            self::USERS_DELETE => 'Deletar usuários',
            self::USERS_SHOW => 'Listar informações de um usuário',

            self::ROLES_LIST => 'Listar perfis',
            self::ROLES_VIEW => 'Visualizar perfis',
            self::ROLES_CREATE => 'Criar perfis',
            self::ROLES_EDIT => 'Editar perfis',
            self::ROLES_DELETE => 'Deletar perfis',

            self::ACTIVITY_LOGS_LIST => 'Listar logs',
        };
    }

    public function resource(): string {
        return explode('.', $this->value)[0];
    }
}
