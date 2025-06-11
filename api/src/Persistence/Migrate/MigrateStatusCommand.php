<?php

declare(strict_types=1);

namespace App\Persistence\Migrate;

use Phinx\Console\PhinxApplication;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class MigrateStatusCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('migrate:status', self::class);
    }

    public function __construct(
        private ConsoleOutput $output,
        private PhinxApplication $phinx,
    ) {
    }

    public function __invoke(): int
    {
        return $this->phinx->doRun(
            new ArrayInput(['status']),
            $this->output,
        );
    }
}
