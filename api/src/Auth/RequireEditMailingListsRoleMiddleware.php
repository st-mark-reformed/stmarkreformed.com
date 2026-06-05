<?php

declare(strict_types=1);

namespace App\Auth;

class RequireEditMailingListsRoleMiddleware extends RequireRoleMiddleware
{
    protected function getRole(): UserRole
    {
        return UserRole::EDIT_MAILING_LISTS;
    }
}
