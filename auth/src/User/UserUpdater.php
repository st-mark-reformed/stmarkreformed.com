<?php

declare(strict_types=1);

namespace App\User;

interface UserUpdater
{
    public function updateUser(User $user): Result;
}
