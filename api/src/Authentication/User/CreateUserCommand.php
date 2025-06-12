<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\User\Email;
use App\Authentication\User\User\Role;
use App\Authentication\User\User\Roles;
use App\Cli\CliQuestion;
use App\Persistence\ResultConsoleResponder;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

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
        private UserRepository $repository,
        private ResultConsoleResponder $responder,
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

        $result = $this->repository->createAndPersist($user);

        $this->responder->respond(
            $result,
            'User Created 👍',
            'User could not be created 😞',
        );
    }
}
