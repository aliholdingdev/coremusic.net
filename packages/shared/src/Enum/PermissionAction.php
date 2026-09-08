<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Enum;

enum PermissionAction: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case MANAGE = 'manage';
}
