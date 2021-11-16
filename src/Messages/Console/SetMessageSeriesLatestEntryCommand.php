<?php

declare(strict_types=1);

namespace App\Messages\Console;

use App\Messages\MessagesApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use yii\helpers\BaseConsole;

class SetMessageSeriesLatestEntryCommand
{
    public function __construct(private MessagesApi $messagesApi)
    {
    }

    public function run(Output $output): void
    {
        $output->writeln(
            'Setting messages series latest entry dates...',
            BaseConsole::FG_YELLOW,
        );

        $this->messagesApi->setMessageSeriesLatestEntry();

        $output->writeln(
            'Finished setting messages series latest entry dates.',
            BaseConsole::FG_GREEN,
        );
    }
}
