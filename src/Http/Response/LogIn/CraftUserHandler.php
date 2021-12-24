<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

use craft\config\GeneralConfig;
use craft\elements\User;
use craft\services\Users;
use craft\web\User as UserSession;
use Exception;

class CraftUserHandler
{
    public function __construct(
        private Users $usersService,
        private GeneralConfig $config,
        private UserSession $userSession,
        private CraftUserHelperFacade $userHelper,
    ) {
    }

    /**
     * @throws Exception
     */
    public function logUserIn(
        string $email,
        string $password,
    ): LogInPayload {
        $user = $this->usersService->getUserByUsernameOrEmail(
            $email,
        );

        if ($user === null || $user->password === null) {
            return $this->handleLoginFailure(
                authError: User::AUTH_INVALID_CREDENTIALS,
            );
        }

        if (! $user->authenticate($password)) {
            return $this->handleLoginFailure(
                authError: $user->authError,
                user: $user,
            );
        }

        if ($this->config->rememberedUserSessionDuration !== 0) {
            $duration = $this->config->rememberedUserSessionDuration;
        } else {
            $duration = $this->config->userSessionDuration;
        }

        if (! $this->userSession->login($user, $duration)) {
            return $this->handleLoginFailure(user: $user);
        }

        return new LogInPayload(succeeded: true);
    }

    /**
     * @throws Exception
     *
     * @phpstan-ignore-next-line
     */
    private function handleLoginFailure(
        ?string $authError = null,
        ?User $user = null
    ): LogInPayload {
        return new LogInPayload(
            succeeded: false,
            message: $this->userHelper->getLoginFailureMessage(
                $authError,
                $user
            ),
        );
    }
}
