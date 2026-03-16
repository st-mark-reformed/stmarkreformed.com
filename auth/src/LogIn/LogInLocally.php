<?php

declare(strict_types=1);

namespace App\LogIn;

use App\User\UserRepository;
use App\User\UserSessionRepository;

readonly class LogInLocally
{
    public function __construct(
        private UserRepository $userRepository,
        private LogInFlashErrorMessages $flashErrorMessages,
        private UserSessionRepository $userSessionRepository,
    ) {
    }

    public function execute(PostData $postData): void
    {
        if (! $postData->isValid) {
            $postData->walkValidationMessages(
                callback: fn (
                    string $message,
                ) => $this->flashErrorMessages->sendToNextRequest(
                    new LogInFlashErrorMessage(title: $message),
                ),
            );

            return;
        }

        $user = $this->userRepository->findByEmail(email: $postData->email);

        if (
            ! $user->isValid ||
            ! $user->isPasswordValid($postData->password)
        ) {
            $this->flashErrorMessages->sendToNextRequest(
                new LogInFlashErrorMessage(
                    title: 'We could not log you in with that email and password.',
                ),
            );

            return;
        }

        $this->userSessionRepository->createPersistentSession(user: $user);
    }
}
