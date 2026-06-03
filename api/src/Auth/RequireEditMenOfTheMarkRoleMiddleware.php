<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditMenOfTheMarkRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_MEN_OF_THE_MARK;
    }
}
