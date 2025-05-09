<?php

declare(strict_types=1);

namespace Config;

use RxAnte\AppBootstrap\Cli\CliConfig;

class BootCliConfigFactory
{
    public function create(): CliConfig
    {
        return new CliConfig(cliAppName: 'SMRC CLI');
    }
}
