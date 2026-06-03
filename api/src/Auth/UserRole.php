<?php

declare(strict_types=1);

namespace App\Auth;

enum UserRole
{
    case EDIT_MESSAGES;
    case EDIT_PROFILES;
    case EDIT_NEWS;
    case EDIT_MEN_OF_THE_MARK;
    case MANAGE_USERS;
}
