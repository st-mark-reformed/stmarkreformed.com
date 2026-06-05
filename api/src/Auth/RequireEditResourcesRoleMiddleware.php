<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditResourcesRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_RESOURCES;
    }
}
