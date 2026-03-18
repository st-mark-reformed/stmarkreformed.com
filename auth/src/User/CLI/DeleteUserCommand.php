<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\UserRepository;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class DeleteUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:delete-user [-e|--email=]',
            self::class,
        )->descriptions(
            'Deletes a user',
            ['--email' => 'Specify the email of the user'],
        )->defaults(['email' => null]);
    }

    public function __construct(
        private CliQuestion $question,
        private UserRepository $userRepository,
        private ConsoleOutputInterface $output,
    ) {
    }

    public function __invoke(string|null $email = null): int
    {
        if ($email === null) {
            $email = $this->question->ask(question: 'Email: ');
        }

        $user = $this->userRepository->findByEmail($email);

        if (! $user->isValid) {
            $this->output->writeln('<fg=red>User not found</>');

            return 1;
        }

        $this->userRepository->deleteUserById($user->id);

        $this->output->writeln('<fg=green>User deleted</>');

        return 0;
    }
}
