<?php

declare(strict_types=1);

namespace App\User;

enum UserRole
{
    case EDIT_MESSAGES;
    case EDIT_PROFILES;
}
