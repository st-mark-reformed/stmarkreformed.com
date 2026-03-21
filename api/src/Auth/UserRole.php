<?php

declare(strict_types=1);

namespace App\Auth;

enum UserRole
{
    case EDIT_MESSAGES;
    case EDIT_PROFILES;
}
