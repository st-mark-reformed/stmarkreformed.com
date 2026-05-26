<?php

declare(strict_types=1);

namespace App\Messages\Search;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class IndexAllMessagesCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'messages:search:index-all',
            self::class,
        );
    }

    public function __construct(private IndexAllMessages $indexAllMessages)
    {
    }

    public function __invoke(): int
    {
        $this->indexAllMessages->index();

        return 0;
    }
}
