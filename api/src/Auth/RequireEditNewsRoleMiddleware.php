<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditNewsRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_NEWS;
    }
}
