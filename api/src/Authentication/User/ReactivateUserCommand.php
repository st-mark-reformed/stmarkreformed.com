<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Persistence\ResultConsoleResponder;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class ReactivateUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:reactivate',
            self::class,
        );
    }

    public function __construct(
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

        if ($user->isActive) {
            $this->output->writeln(
                '<fg=green>User is already active 👍</>',
            );

            return;
        }

        $user = $user->withIsActive(true);

        $result = $this->repository->persist($user);

        $this->responder->respond(
            $result,
            'User reactivated successfully 👍',
            'User could not be reactivated 😞',
        );
    }
}
