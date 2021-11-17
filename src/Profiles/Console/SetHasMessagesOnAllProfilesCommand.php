<?php

declare(strict_types=1);

namespace App\Profiles\Console;

use App\Profiles\ProfilesApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use yii\helpers\BaseConsole;

class SetHasMessagesOnAllProfilesCommand
{
    public function __construct(private ProfilesApi $profilesApi)
    {
    }

    public function run(Output $output): void
    {
        $output->writeln(
            'Updating all profiles...',
            BaseConsole::FG_YELLOW,
        );

        $this->profilesApi->setHasMessagesOnAllProfiles();

        $output->writeln(
            'Finished updating all profiles.',
            BaseConsole::FG_GREEN,
        );
    }
}
