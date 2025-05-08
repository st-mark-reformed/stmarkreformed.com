<?php

declare(strict_types=1);

namespace App\ElasticSearch\Console;

use App\ElasticSearch\ElasticSearchApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use yii\helpers\BaseConsole;

class IndexAllMessagesCommand
{
    public function __construct(private ElasticSearchApi $elasticSearchApi)
    {
    }

    public function run(Output $output): void
    {
        $output->writeln(
            'Indexing all messages...',
            BaseConsole::FG_YELLOW,
        );

        $this->elasticSearchApi->indexAllMessages();

        $output->writeln(
            'Finished indexing messages.',
            BaseConsole::FG_GREEN,
        );
    }
}
