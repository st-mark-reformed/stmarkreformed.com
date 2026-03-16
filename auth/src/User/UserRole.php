<?php

declare(strict_types=1);

namespace App\User;

enum UserRole
{
    case ADMIN;
    case EDIT_MESSAGES;
}
