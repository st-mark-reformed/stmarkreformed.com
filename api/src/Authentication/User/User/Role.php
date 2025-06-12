<?php

declare(strict_types=1);

namespace App\Authentication\User\User;

enum Role
{
    case INVALID;
    case CMS;

    public static function createFromName(string $role): Role
    {
        return match ($role) {
            Role::CMS->name => Role::CMS,
            default => Role::INVALID,
        };
    }

    public static function createFromHumanReadable(string $role): Role
    {
        return match ($role) {
            Role::CMS->humanReadable() => Role::CMS,
            default => Role::INVALID,
        };
    }

    public function humanReadable(): string
    {
        return match ($this) {
            Role::INVALID => 'Invalid',
            Role::CMS => 'CMS User',
        };
    }
}
