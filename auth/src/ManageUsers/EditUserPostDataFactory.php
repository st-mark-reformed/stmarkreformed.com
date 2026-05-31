<?php

declare(strict_types=1);

namespace App\ManageUsers;

use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_values;
use function is_array;
use function is_string;

readonly class EditUserPostDataFactory
{
    public function createFromRequest(
        ServerRequestInterface $request,
    ): EditUserPostData {
        $postData = $request->getParsedBody();
        $postData = is_array($postData) ? $postData : [];

        $email = $postData['email'] ?? '';
        $email = is_string($email) ? $email : '';

        $roles = $postData['roles'] ?? [];
        $roles = is_array($roles) ? $roles : [];

        return new EditUserPostData(
            email: $email,
            roleNames: array_values(array_filter(
                $roles,
                static fn (mixed $role): bool => is_string($role),
            )),
        );
    }
}
