<?php

declare(strict_types=1);

namespace Cli;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Silly\Command\Command;
use stdClass;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint

readonly class ApplyCliCommandsEventForAliases extends ApplyCliCommandsEvent
{
    public stdClass $expression;

    /** @phpstan-ignore-next-line */
    public function __construct()
    {
        $this->expression = new stdClass();
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function addCommand(
        string $expression,
        callable|array|string $callable,
        array $aliases = [],
    ): Command {
        $this->expression->expression = $expression;

        return new class extends Command {
            /** @phpstan-ignore-next-line */
            public function descriptions(
                $description,
                array $argumentAndOptionDescriptions = [],
            ) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            public function defaults(array $defaults = [])
            {
                return $this;
            }
        };
    }
}
