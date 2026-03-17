<?php

declare(strict_types=1);

namespace App\User\CLI;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

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

    public function __construct(private ListUserRoles $listUserRoles)
    {
    }

    public function __invoke(string|null $email = null): int
    {
        return (int) $this->listUserRoles->list($email)->success;
    }
}
