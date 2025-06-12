<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\User\Role;
use App\Cli\CliQuestion;
use App\Persistence\ResultConsoleResponder;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

use function array_filter;
use function array_map;
use function implode;

readonly class RemoveUserRoleCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:remove-role',
            self::class,
        );
    }

    public function __construct(
        private CliQuestion $question,
        private ConsoleOutput $output,
        private UserRepository $repository,
        private ResultConsoleResponder $responder,
        private CliFindUserByEmail $findUserByEmail,
    ) {
    }

    public function __invoke(): void
    {
        $user = $this->findUserByEmail->find();

        if ($user === null) {
            return;
        }

        $rolesDisplay = implode('|', array_filter(
            array_map(
                static fn (Role $r) => $r->name,
                Role::cases(),
            ),
            static fn (string $role) => $role !== 'INVALID',
        ));

        $roleString = $this->question->ask(
            'Roles (' . $rolesDisplay . '): ',
            true,
        );

        $role = Role::createFromName($roleString);

        if ($role->name === Role::INVALID->name) {
            $this->output->writeln(
                '<fg=red>Invalid role specified 😞</>',
            );

            return;
        }

        if (! $user->roles->hasRole($role)) {
            $this->output->writeln(
                '<fg=green>User already does not have role 👍</>',
            );

            return;
        }

        $user = $user->withRemovedRole($role);

        $result = $this->repository->persist($user);

        $this->responder->respond(
            $result,
            'User role removed successfully 👍',
            'User role could not be removed 😞',
        );
    }
}
