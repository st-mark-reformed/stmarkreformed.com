<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\User\UserRepository;
use App\User\UserRole;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function array_map;
use function constant;

readonly class AddUserRolesCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:add-roles [-e|--email=] [-r|--role=]*',
            self::class,
        )->descriptions(
            'Add roles to a user',
            ['--email' => 'Specify the email of the user'],
        )->defaults(['email' => null]);
    }

    public function __construct(
        private CliCollectRole $collectRole,
        private ListUserRoles $listUserRoles,
        private UserRepository $userRepository,
        private CliResultHandler $cliResultHandler,
    ) {
    }

    /** @param string[] $role */
    public function __invoke(
        string|null $email = null,
        array $role = [],
    ): int {
        $listResult = $this->listUserRoles->list($email);

        if (! $listResult->success) {
            return 1;
        }

        $role = $this->collectRole->collect($role);

        $updatedUser = $listResult->user->withAddedRoles(array_map(
            static fn (
                string $role,
                /** @phpstan-ignore-next-line */
            ): UserRole => constant(UserRole::class . '::' . $role),
            $role,
        ));

        $result = $this->userRepository->updateUser($updatedUser);

        return $this->cliResultHandler->handleResult(
            result: $result,
            successMessage: 'User created successfully',
            errorMessage: 'Unable to create user:',
        );
    }
}
