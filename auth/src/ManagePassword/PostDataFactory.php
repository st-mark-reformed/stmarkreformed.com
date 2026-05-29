<?php

declare(strict_types=1);

namespace App\ManagePassword;

use Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function is_string;

readonly class PostDataFactory
{
    public function createFromRequest(ServerRequestInterface $request): PostData
    {
        $postData = $request->getParsedBody();
        $postData = is_array($postData) ? $postData : [];

        $currentPassword = $postData['current_password'] ?? '';
        $currentPassword = is_string($currentPassword) ? $currentPassword : '';

        $newPassword = $postData['new_password'] ?? '';
        $newPassword = is_string($newPassword) ? $newPassword : '';

        $confirmPassword = $postData['confirm_password'] ?? '';
        $confirmPassword = is_string($confirmPassword) ? $confirmPassword : '';

        return new PostData(
            currentPassword: $currentPassword,
            newPassword: $newPassword,
            confirmPassword: $confirmPassword,
        );
    }
}
