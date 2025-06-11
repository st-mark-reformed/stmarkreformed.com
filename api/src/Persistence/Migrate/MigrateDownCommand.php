<?php

declare(strict_types=1);

namespace App\Persistence\Migrate;

use App\Cli\Question;
use Phinx\Console\PhinxApplication;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class MigrateDownCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('migrate:down', self::class);
    }

    public function __construct(
        private Question $question,
        private ConsoleOutput $output,
        private PhinxApplication $phinx,
    ) {
    }

    public function __invoke(): int
    {
        $params = ['rollback'];

        $target = $this->question->ask(
            '<fg=cyan>Specify target (0 to revert all, blank to revert last): </>',
            false,
        );

        if ($target !== '') {
            $params['--target'] = $target;
        }

        return $this->phinx->doRun(
            new ArrayInput($params),
            $this->output,
        );
    }
}
