<?php

declare(strict_types=1);

namespace App\Craft\Commands;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
use App\Profiles\Console\SetHasMessagesOnAllProfilesCommand;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use Config\di\Container;
use yii\console\Controller;

use function assert;

/**
 * @codeCoverageIgnore
 */
class ProfilesConsoleController extends Controller
{
    private SetHasMessagesOnAllProfilesCommand $setHasMessagesOnAllProfilesCommand;

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

        $setHasMessagesOnAllProfilesCommand = $container->get(
            SetHasMessagesOnAllProfilesCommand::class,
        );

        assert(
            $setHasMessagesOnAllProfilesCommand instanceof
                SetHasMessagesOnAllProfilesCommand
        );

        $this->setHasMessagesOnAllProfilesCommand = $setHasMessagesOnAllProfilesCommand;
    }

    public function actionSetHasMessagesOnAllProfiles(): void
    {
        $this->setHasMessagesOnAllProfilesCommand->run(output: $this->output);
    }
}
