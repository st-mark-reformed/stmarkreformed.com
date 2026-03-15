<?php

declare(strict_types=1);

namespace Config;

use RxAnte\AppBootstrap\BootConfig;
use RxAnte\AppBootstrap\RuntimeConfig;

class BootConfigFactory
{
    public function create(bool $isCli): BootConfig
    {
        $runtimeConfig = new RuntimeConfig();

        return new BootConfig(
            isCli: $isCli,
            useWhoopsErrorHandling: $runtimeConfig->getBoolean(
                RuntimeConfigOptions::USE_WHOOPS_ERROR_HANDLING,
            ),
        );
    }
}
