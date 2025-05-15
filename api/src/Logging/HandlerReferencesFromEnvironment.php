<?php

declare(strict_types=1);

namespace App\Logging;

use App\Logging\HandlerFactories\HandlerFactory;
use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;

use function array_map;
use function explode;

readonly class HandlerReferencesFromEnvironment
{
    public function __construct(
        private RuntimeConfig $config,
    ) {
    }

    public function create(): HandlerFactoryReferenceCollection
    {
        $classStrings = explode(',', $this->config->getString(
            RuntimeConfigOptions::LOG_HANDLER_FACTORIES,
        ));

        $references = array_map(
            /** @param class-string<HandlerFactory> $classString */
            static fn (string $classString) => new HandlerFactoryReference(
            /** @phpstan-ignore-next-line */
                $classString,
            ),
            $classStrings,
        );

        return new HandlerFactoryReferenceCollection($references);
    }
}
