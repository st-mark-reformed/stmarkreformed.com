<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Up;

readonly class UpConfig
{
    public function __construct(
        public bool $build = false,
        public bool $inPlace = false,
        public bool $skipProvision = false,
    ) {
    }
}
