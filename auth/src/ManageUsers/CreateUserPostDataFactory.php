<?php

declare(strict_types=1);

namespace App\ManageUsers;

use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_values;
use function is_array;
use function is_string;

readonly class CreateUserPostDataFactory
{
    public function createFromRequest(
        ServerRequestInterface $request,
    ): CreateUserPostData {
        $postData = $request->getParsedBody();
        $postData = is_array($postData) ? $postData : [];

        $email = $postData['email'] ?? '';
        $email = is_string($email) ? $email : '';

        $password = $postData['password'] ?? '';
        $password = is_string($password) ? $password : '';

        $confirmPassword = $postData['confirm_password'] ?? '';
        $confirmPassword = is_string($confirmPassword) ? $confirmPassword : '';

        $roles = $postData['roles'] ?? [];
        $roles = is_array($roles) ? $roles : [];

        return new CreateUserPostData(
            email: $email,
            password: $password,
            confirmPassword: $confirmPassword,
            roleNames: array_values(array_filter(
                $roles,
                static fn (mixed $role): bool => is_string($role),
            )),
        );
    }
}
