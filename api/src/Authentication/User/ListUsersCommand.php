<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\User\Role;
use App\Authentication\User\User\User;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

use function implode;

readonly class ListUsersCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('user:list', self::class);
    }

    public function __construct(
        private ConsoleOutput $output,
        private UserRepository $repository,
    ) {
    }

    public function __invoke(): void
    {
        $table = new Table($this->output);
        $table->setHeaders(['ID', 'Email', 'Roles', 'Is Active']);

        $rows = $this->repository->findAllUsers()->mapToArray(
            static function (User $user): array {
                return [
                    $user->id->toString(),
                    $user->email->address,
                    implode(
                        '|',
                        $user->roles->mapToArray(
                            static fn (Role $r) => $r->name,
                        ),
                    ),
                    $user->isActive ? 'Yes' : 'No',
                ];
            },
        );

        $table->setRows($rows);
        $table->render();
    }
}
