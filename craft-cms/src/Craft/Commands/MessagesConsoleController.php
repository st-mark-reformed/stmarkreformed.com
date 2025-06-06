<?php

declare(strict_types=1);

namespace App\Craft\Commands;

use App\Http\Response\Media\Messages\GenerateMessagesPagesForRedis;
use App\Messages\Console\SetMessageSeriesLatestEntryCommand;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use Config\di\Container;
use yii\console\Controller;

use function assert;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

/**
 * @codeCoverageIgnore
 */
class MessagesConsoleController extends Controller
{
    private SetMessageSeriesLatestEntryCommand $setMessageSeriesLatestEntryCommand;

    private GenerateMessagesPagesForRedis $generateMessagesPagesForRedis;

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

        $setMessageSeriesLatestEntryCommand = $container->get(SetMessageSeriesLatestEntryCommand::class);
        assert($setMessageSeriesLatestEntryCommand instanceof SetMessageSeriesLatestEntryCommand);
        $this->setMessageSeriesLatestEntryCommand = $setMessageSeriesLatestEntryCommand;

        $generateMessagesPagesForRedis = $container->get(GenerateMessagesPagesForRedis::class);
        assert($generateMessagesPagesForRedis instanceof GenerateMessagesPagesForRedis);
        $this->generateMessagesPagesForRedis = $generateMessagesPagesForRedis;
    }

    public function actionSetMessageSeriesLatestEntryCommand(): void
    {
        $this->setMessageSeriesLatestEntryCommand->run(output: $this->output);
    }

    public function actionGenerateMessagePagesForRedis(): void
    {
        $this->generateMessagesPagesForRedis->generate();
    }
}
