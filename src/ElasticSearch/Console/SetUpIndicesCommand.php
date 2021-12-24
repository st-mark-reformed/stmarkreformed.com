<?php

declare(strict_types=1);

namespace App\ElasticSearch\Console;

use App\ElasticSearch\ElasticSearchApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use yii\helpers\BaseConsole;

class SetUpIndicesCommand
{
    public function __construct(private ElasticSearchApi $elasticSearchApi)
    {
    }

    public function run(Output $output): void
    {
        $output->writeln(
            'Setting up indices...',
            BaseConsole::FG_YELLOW,
        );

        $this->elasticSearchApi->setUpIndices();

        $output->writeln(
            'Finished setting up indices.',
            BaseConsole::FG_GREEN,
        );
    }
}
