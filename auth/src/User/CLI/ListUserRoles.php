<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\UserRepository;
use App\User\UserRole;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class ListUserRoles
{
    public function __construct(
        private CliQuestion $question,
        private UserRepository $userRepository,
        private ConsoleOutputInterface $output,
    ) {
    }

    public function list(string|null $email = null): ListUserRolesResult
    {
        if ($email === null) {
            $email = $this->question->ask(question: 'Email: ');
        }

        $user = $this->userRepository->findByEmail($email);

        if (! $user->isValid) {
            $this->output->writeln('<fg=red>User not found</>');

            return new ListUserRolesResult(success: false, user: $user);
        }

        if ($user->roles->count() < 1) {
            $this->output->writeln('<fg=yellow>User has no roles</>');

            return new ListUserRolesResult(success: true, user: $user);
        }

        $this->output->writeln(
            '<fg=cyan;options=bold>Current User roles:</>',
        );

        $user->roles->walk(function (UserRole $role): void {
            $this->output->writeln('<fg=cyan>' . $role->name . '</>');
        });

        return new ListUserRolesResult(success: true, user: $user);
    }
}
