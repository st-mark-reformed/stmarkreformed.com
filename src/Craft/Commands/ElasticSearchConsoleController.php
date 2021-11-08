<?php

declare(strict_types=1);

namespace App\Craft\Commands;

use App\ElasticSearch\Console\SetUpIndicesCommand;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use Config\di\Container;
use yii\console\Controller;

use function assert;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

/**
 * @psalm-suppress MixedArgument
 * @psalm-suppress MissingParamType
 * @codeCoverageIgnore
 */
class ElasticSearchConsoleController extends Controller
{
    private SetUpIndicesCommand $setUpIndicesCommand;

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
    }

    public function actionSetUpIndices(): void
    {
        $this->setUpIndicesCommand->run(output: $this->output);
    }
}
