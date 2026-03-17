<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\UserRepository;
use App\User\UserRole;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class ListUserRolesCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:list-roles [-e|--email=]',
            self::class,
        )->descriptions(
            'List a user\'s roles',
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

        if ($user->roles->count() < 1) {
            $this->output->writeln('<fg=yellow>User has no roles</>');

            return 0;
        }

        $user->roles->walk(function (UserRole $role): void {
            $this->output->writeln('<fg=cyan>' . $role->name . '</>');
        });

        return 0;
    }
}
