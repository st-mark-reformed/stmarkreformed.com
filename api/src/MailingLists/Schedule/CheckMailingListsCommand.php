<?php

declare(strict_types=1);

namespace App\MailingLists\Schedule;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class CheckMailingListsCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'mailing-lists:check',
            self::class,
        );
    }

    public function __construct(private CheckMailingListsJob $job)
    {
    }

    public function __invoke(): int
    {
        $this->job->check();

        return 0;
    }
}
