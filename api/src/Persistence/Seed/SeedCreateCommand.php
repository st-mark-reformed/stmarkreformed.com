<?php

declare(strict_types=1);

namespace App\Persistence\Seed;

use App\Cli\Question;
use App\Persistence\CaseConversion;
use Phinx\Console\PhinxApplication;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class SeedCreateCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('seed:create', self::class);
    }

    public function __construct(
        private Question $question,
        private ConsoleOutput $output,
        private PhinxApplication $phinx,
        private CaseConversion $caseConversion,
    ) {
    }

    public function __invoke(): int
    {
        $name = $this->question->ask(
            question: '<fg=cyan>Provide a seed name: </>',
            required: true,
        );

        $pascaleName = $this->caseConversion->toPascale(string: $name);

        return $this->phinx->doRun(
            new ArrayInput([
                0 => 'seed:create',
                'name' => $pascaleName,
            ]),
            $this->output,
        );
    }
}
