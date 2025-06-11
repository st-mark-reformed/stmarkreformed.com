<?php

declare(strict_types=1);

namespace App\Persistence\Seed;

use Phinx\Console\PhinxApplication;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class SeedRunCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('seed:run', self::class);
    }

    public function __construct(
        private ConsoleOutput $output,
        private PhinxApplication $phinx,
    ) {
    }

    /**
     * TODO: We should probably build in support for running specific seeds at
     * some point.
     *
     * @see https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function __invoke(): int
    {
        return $this->phinx->doRun(
            new ArrayInput(['seed:run']),
            $this->output,
        );
    }
}
