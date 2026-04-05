<?php

declare(strict_types=1);

namespace App\Messages\Search;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class SetUpIndicesCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'messages:search:set-up-indices',
            self::class,
        );
    }

    public function __construct(private SetUpIndices $setUpIndices)
    {
    }

    public function __invoke(): int
    {
        $this->setUpIndices->setUp();

        return 0;
    }
}
