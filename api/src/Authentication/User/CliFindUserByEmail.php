<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\User\User;
use App\Cli\CliQuestion;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class CliFindUserByEmail
{
    public function __construct(
        private CliQuestion $question,
        private ConsoleOutput $output,
        private UserRepository $repository,
    ) {
    }

    public function find(): User|null
    {
        $email = $this->question->ask('Email: ', true);

        $user = $this->repository->findByEmail($email);

        if ($user === null) {
            $this->output->writeln(
                '<fg=red>User could not be found 😞</>',
            );

            return null;
        }

        return $user;
    }
}
