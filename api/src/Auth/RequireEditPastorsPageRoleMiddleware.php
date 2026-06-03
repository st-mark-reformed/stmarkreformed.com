<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditPastorsPageRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_PASTORS_PAGE;
    }
}
