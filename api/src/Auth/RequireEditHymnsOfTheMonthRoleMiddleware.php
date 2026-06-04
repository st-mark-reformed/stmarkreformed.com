<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditHymnsOfTheMonthRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_HYMNS_OF_THE_MONTH;
    }
}
