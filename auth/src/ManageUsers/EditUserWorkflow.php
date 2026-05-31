<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\User;
use App\User\UserUpdater;

use function array_map;

readonly class EditUserWorkflow
{
    public function __construct(
        private UserUpdater $userUpdater,
        private RoleNamesToUserRoles $roleNamesToUserRoles,
        private ManageUsersFlashMessages $flashMessages,
    ) {
    }

    public function execute(User $existingUser, EditUserPostData $postData): bool
    {
        if (! $postData->isValid) {
            $postData->walkValidationMessages(
                fn (string $message) => $this->flashMessages->sendError($message),
            );

            return false;
        }

        $updatedUser = $existingUser->with(
            email: $postData->email,
            roles: $this->roleNamesToUserRoles->create($postData->roleNames),
        );

        $result = $this->userUpdater->updateUser($updatedUser);

        if (! $result->success) {
            array_map(
                fn (string $message) => $this->flashMessages->sendError($message),
                $result->errors,
            );

            return false;
        }

        $this->flashMessages->sendSuccess(
            'User "' . $postData->email->toString() . '" was updated.',
        );

        return true;
    }
}
