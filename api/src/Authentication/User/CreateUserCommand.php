<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\User\Email;
use App\Authentication\User\User\Role;
use App\Authentication\User\User\Roles;
use App\Cli\CliQuestion;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

use function array_filter;
use function array_map;
use function explode;
use function implode;

readonly class CreateUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('user:create', self::class);
    }

    public function __construct(
        private CliQuestion $question,
        private ConsoleOutput $output,
        private UserRepository $repository,
    ) {
    }

    public function __invoke(): void
    {
        $email = $this->question->ask('Email: ', true);

        $rolesDisplay = implode('|', array_filter(
            array_map(
                static fn (Role $r) => $r->name,
                Role::cases(),
            ),
            static fn (string $role) => $role !== 'INVALID',
        ));

        $rolesString = $this->question->ask(
            'Roles (' . $rolesDisplay . '): ',
        );

        if ($rolesString !== '') {
            $roles = new Roles(array_map(
                static fn (string $role) => Role::createFromName(
                    $role,
                ),
                explode('|', $rolesString),
            ));
        } else {
            $roles = new Roles([]);
        }

        $user = new User\User(
            new Email($email),
            $roles,
            true,
        );

        $result = $this->repository->createAndPersistUser($user);

        if ($result->success) {
            $this->output->writeln(
                '<fg=green>User Created 👍</>',
            );

            return;
        }

        $this->output->writeln(
            '<fg=red>User could not be created 😞</>',
        );

        array_map(
            function (string $msg): void {
                $this->output->writeln(implode('', [
                    '<fg=red>',
                    $msg,
                    '</>',
                ]));
            },
            $result->messages,
        );
    }
}
