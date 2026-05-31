<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\NewUser;
use App\User\UserRepository;

use function array_map;

readonly class CreateUserWorkflow
{
    public function __construct(
        private UserRepository $userRepository,
        private RoleNamesToUserRoles $roleNamesToUserRoles,
        private ManageUsersFlashMessages $flashMessages,
    ) {
    }

    public function execute(CreateUserPostData $postData): bool
    {
        if (! $postData->isValid) {
            $postData->walkValidationMessages(
                fn (string $message) => $this->flashMessages->sendError($message),
            );

            return false;
        }

        $result = $this->userRepository->createUser(new NewUser(
            email: $postData->email,
            password: $postData->password,
            roles: $this->roleNamesToUserRoles->create($postData->roleNames),
        ));

        if (! $result->success) {
            array_map(
                fn (string $message) => $this->flashMessages->sendError($message),
                $result->errors,
            );

            return false;
        }

        $this->flashMessages->sendSuccess(
            'User "' . $postData->email->toString() . '" was created.',
        );

        return true;
    }
}
