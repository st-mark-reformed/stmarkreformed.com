<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\NewUser;
use App\User\UserEmail;
use App\User\UserRepository;
use App\User\UserRole;
use App\User\UserRoles;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function array_filter;
use function array_map;
use function constant;
use function count;
use function explode;
use function implode;
use function trim;

readonly class CreateNewUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'user:create [-e|--email=] [-p|--password=] [-r|--role=]*',
            self::class,
        )->descriptions(
            'Create a new user',
            [
                '--email' => 'Specify the email of the user',
                '--password' => 'Specify the password of the user',
                '--role' => 'Specify the role(s) of the user. Valid options: ' . implode(
                    ', ',
                    array_map(
                        static fn (UserRole $r) => $r->name,
                        UserRole::cases(),
                    ),
                ),
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
    ) {
    }

    /** @param string[] $role */
    public function __invoke(
        string|null $email = null,
        string|null $password = null,
        array $role = [],
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

        if (count($role) < 1) {
            $role = array_map(
                static fn (string $r) => trim($r),
                array_filter(
                    explode(',', $this->question->ask(
                        'Role(s) ' . implode(
                            ', ',
                            array_map(
                                static fn (UserRole $r) => $r->name,
                                UserRole::cases(),
                            ),
                        ) . ': ',
                    )),
                    static fn (string $r) => $r !== '',
                ),
            );
        }

        $result = $this->userRepository->createUser(new NewUser(
            email: new UserEmail($email),
            password: $password,
            /** @phpstan-ignore-next-line */
            roles: new UserRoles(array_map(
                static fn (string $r) => constant(
                    UserRole::class . '::' . $r,
                ),
                $role,
            )),
        ));

        if ($result->success) {
            $this->output->writeln(
                '<fg=green>User created successfully</>',
            );

            return 0;
        }

        $this->output->writeln(
            '<fg=red;options=bold>Unable to create user:</>',
        );

        array_map(
            function (string $error): void {
                $this->output->writeln(
                    '<fg=red>' . $error . '</>',
                );
            },
            $result->errors,
        );

        return 1;
    }
}
