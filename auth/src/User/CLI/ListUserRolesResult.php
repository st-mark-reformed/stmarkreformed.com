<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\User\User;

readonly class ListUserRolesResult
{
    public function __construct(
        public bool $success,
        public User $user,
    ) {
    }
}
