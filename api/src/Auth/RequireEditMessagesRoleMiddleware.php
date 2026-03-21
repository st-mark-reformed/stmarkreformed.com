<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditMessagesRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_MESSAGES;
    }
}
