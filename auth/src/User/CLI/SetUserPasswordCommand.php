<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\UserRepository;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class SetUserPasswordCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:set-password [-e|--email=] [-p|--password=]',
            self::class,
        )->descriptions(
            'Set user password',
            [
                '--email' => 'Specify the email of the user',
                '--password' => 'Specify the password of the user',
            ],
        )->defaults([
            'email' => null,
            'password' => null,
        ]);
    }

    public function __construct(
        private CliQuestion $question,
        private UserRepository $userRepository,
        private ConsoleOutputInterface $output,
        private CliResultHandler $cliResultHandler,
    ) {
    }

    public function __invoke(
        string|null $email = null,
        string|null $password = null,
    ): int {
        if ($email === null) {
            $email = $this->question->ask(question: 'Email: ');
        }

        if ($password === null) {
            $password = $this->question->ask(
                question: 'Password: ',
                hidden: true,
            );
        }

        $user = $this->userRepository->findByEmail($email);

        if (! $user->isValid) {
            $this->output->writeln('<fg=red>User not found</>');

            return 1;
        }

        $user = $user->withNewPassword($password);

        $result = $this->userRepository->updateUser($user);

        return $this->cliResultHandler->handleResult(
            result: $result,
            successMessage: 'Password set successfully',
            errorMessage: 'Unable to set user password:',
        );
    }
}
