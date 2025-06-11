<?php

declare(strict_types=1);

namespace App\Authentication;

use RuntimeException;

enum Role
{
    case CMS_USER;

    public static function createFromHumanReadable(string $role): Role
    {
        return match ($role) {
            Role::CMS_USER->humanReadable() => Role::CMS_USER,
            default => throw new RuntimeException(
                $role . ' is not a valid UserRole',
            ),
        };
    }

    public function humanReadable(): string
    {
        return match ($this) {
            Role::CMS_USER => 'CMS User',
        };
    }
}
