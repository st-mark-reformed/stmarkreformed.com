<?php

declare(strict_types=1);

namespace App\ManagePassword;

use App\User\UserSession;
use App\User\UserSessionRepository;
use App\User\UserUpdater;

use function array_map;

readonly class ChangePasswordWorkflow
{
    public function __construct(
        private UserUpdater $userUpdater,
        private UserSessionRepository $userSessionRepository,
        private ManagePasswordFlashMessages $flashMessages,
    ) {
    }

    public function execute(UserSession $session, PostData $postData): void
    {
        $user = $session->user;

        if (! $postData->isValid) {
            $postData->walkValidationMessages(
                callback: fn (
                    string $message,
                ) => $this->flashMessages->sendToNextRequest(
                    new ManagePasswordFlashMessage(
                        title: $message,
                        type: MessageType::error,
                    ),
                ),
            );

            return;
        }

        if (! $user->isPasswordValid($postData->currentPassword)) {
            $this->flashMessages->sendToNextRequest(
                new ManagePasswordFlashMessage(
                    title: 'Your current password is incorrect.',
                    type: MessageType::error,
                ),
            );

            return;
        }

        $updatedUser = $user->withNewPassword($postData->newPassword);

        $result = $this->userUpdater->updateUser($updatedUser);

        if (! $result->success) {
            array_map(
                fn (
                    string $message,
                ) => $this->flashMessages->sendToNextRequest(
                    new ManagePasswordFlashMessage(
                        title: $message,
                        type: MessageType::error,
                    ),
                ),
                $result->errors,
            );

            return;
        }

        $this->userSessionRepository->refreshSessionUser(
            session: $session,
            user: $updatedUser,
        );

        $this->flashMessages->sendToNextRequest(
            new ManagePasswordFlashMessage(
                title: 'Your password has been updated.',
                type: MessageType::success,
            ),
        );
    }
}
