<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Cli\CliQuestion;
use App\Persistence\ResultConsoleResponder;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class DeactivateUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:deactivate',
            self::class,
        );
    }

    public function __construct(
        private CliQuestion $question,
        private ConsoleOutput $output,
        private UserRepository $repository,
        private ResultConsoleResponder $responder,
    ) {
    }

    public function __invoke(): void
    {
        $email = $this->question->ask('Email: ', true);

        $user = $this->repository->findByEmail($email);

        if ($user === null) {
            $this->output->writeln(
                '<fg=red>User could not be found 😞</>',
            );

            return;
        }

        if (! $user->isActive) {
            $this->output->writeln(
                '<fg=green>User is already deactivated 👍</>',
            );

            return;
        }

        $user = $user->withIsActive(false);

        $result = $this->repository->persist($user);

        $this->responder->respond(
            $result,
            'User deactivated successfully 👍',
            'User could not be deactivated 😞',
        );
    }
}
