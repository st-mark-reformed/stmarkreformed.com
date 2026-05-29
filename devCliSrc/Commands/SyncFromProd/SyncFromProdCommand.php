<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function implode;

readonly class SyncFromProdCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            implode(' ', [
                'prod:sync',
                '[--db-only]',
                '[--files-only]',
            ]),
            self::class,
        )->descriptions(
            'Pulls the latest production backup from Ivy and applies it locally (use --help to see arguments)',
            [
                '--db-only' => 'Only sync the databases (skip files)',
                '--files-only' => 'Only sync the file directories (skip databases)',
            ],
        );
    }

    public function __construct(
        private LatestBackupResolver $latestBackupResolver,
        private DatabaseSync $databaseSync,
        private FilesSync $filesSync,
        private ConsoleOutputInterface $output,
    ) {
    }

    public function __invoke(
        bool $dbOnly = false,
        bool $filesOnly = false,
    ): void {
        $this->run(new SyncFromProdConfig(
            dbOnly: $dbOnly,
            filesOnly: $filesOnly,
        ));
    }

    public function run(
        SyncFromProdConfig $config = new SyncFromProdConfig(),
    ): void {
        $this->output->writeln(
            '<fg=cyan>Resolving the latest production backup on Ivy…</>',
        );

        $backup = $this->latestBackupResolver->resolve();

        $this->output->writeln(
            '<fg=green>Latest backup: ' . $backup->timestamp . '</>',
        );

        if ($config->syncDatabases) {
            $this->databaseSync->run($backup);
        }

        if ($config->syncFiles) {
            $this->filesSync->run($backup);
        }

        $this->output->writeln(
            '<fg=green>Sync from production complete.</>',
        );
    }
}
