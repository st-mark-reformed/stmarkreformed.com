<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\User;
use App\User\UserUpdater;

use function array_map;

readonly class ResetPasswordWorkflow
{
    public function __construct(
        private UserUpdater $userUpdater,
        private ManageUsersFlashMessages $flashMessages,
    ) {
    }

    public function execute(
        User $existingUser,
        ResetPasswordPostData $postData,
    ): bool {
        if (! $postData->isValid) {
            $postData->walkValidationMessages(
                fn (string $message) => $this->flashMessages->sendError($message),
            );

            return false;
        }

        $updatedUser = $existingUser->withNewPassword($postData->newPassword);

        $result = $this->userUpdater->updateUser($updatedUser);

        if (! $result->success) {
            array_map(
                fn (string $message) => $this->flashMessages->sendError($message),
                $result->errors,
            );

            return false;
        }

        $this->flashMessages->sendSuccess(
            'The password for "' . $existingUser->email->toString()
            . '" was reset.',
        );

        return true;
    }
}
