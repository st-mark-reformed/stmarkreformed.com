<?php

declare(strict_types=1);

namespace App\ManageUsers;

use Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function is_string;

readonly class ResetPasswordPostDataFactory
{
    public function createFromRequest(
        ServerRequestInterface $request,
    ): ResetPasswordPostData {
        $postData = $request->getParsedBody();
        $postData = is_array($postData) ? $postData : [];

        $newPassword = $postData['new_password'] ?? '';
        $newPassword = is_string($newPassword) ? $newPassword : '';

        $confirmPassword = $postData['confirm_password'] ?? '';
        $confirmPassword = is_string($confirmPassword) ? $confirmPassword : '';

        return new ResetPasswordPostData(
            newPassword: $newPassword,
            confirmPassword: $confirmPassword,
        );
    }
}
