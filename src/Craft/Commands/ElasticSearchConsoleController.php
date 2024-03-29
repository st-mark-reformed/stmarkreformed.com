<?php

declare(strict_types=1);

namespace App\Craft\Commands;

use App\ElasticSearch\Console\IndexAllMessagesCommand;
use App\ElasticSearch\Console\SetUpIndicesCommand;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use Config\di\Container;
use yii\console\Controller;

use function assert;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

/**
 * @codeCoverageIgnore
 */
class ElasticSearchConsoleController extends Controller
{
    private SetUpIndicesCommand $setUpIndicesCommand;
    private IndexAllMessagesCommand $indexAllMessagesCommand;

    /**
     * @phpstan-ignore-next-line
     */
    public function __construct(
        $id,
        $module,
        $config,
        private Output $output,
    ) {
        parent::__construct(
            $id,
            $module,
            $config,
        );

        $container = Container::get();

        $setUpIndicesCommand = $container->get(SetUpIndicesCommand::class);

        assert($setUpIndicesCommand instanceof SetUpIndicesCommand);

        $this->setUpIndicesCommand = $setUpIndicesCommand;

        $indexAllMessagesCommand = $container->get(
            IndexAllMessagesCommand::class
        );

        assert(
            $indexAllMessagesCommand instanceof IndexAllMessagesCommand
        );

        $this->indexAllMessagesCommand = $indexAllMessagesCommand;
    }

    public function actionSetUpIndices(): void
    {
        $this->setUpIndicesCommand->run(output: $this->output);
    }

    public function actionIndexAllMessages(): void
    {
        $this->indexAllMessagesCommand->run(output: $this->output);
    }
}
