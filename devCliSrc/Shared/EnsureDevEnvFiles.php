<?php

declare(strict_types=1);

namespace Cli\Shared;

use Cli\CliSrcPath;

use function touch;

readonly class EnsureDevEnvFiles
{
    public function __construct(private CliSrcPath $path)
    {
    }

    public function run(): void
    {
        // API
        touch($this->path->pathFromProjectRoot(
            'docker/api/.bash_history',
        ));
        touch($this->path->pathFromProjectRoot(
            'docker/api/.env.local',
        ));

        // App
        touch($this->path->pathFromProjectRoot(
            'docker/application/.env.local',
        ));

        // Proxy
        touch($this->path->pathFromProjectRoot(
            'docker/proxy/.env.local',
        ));

        // Web
        touch($this->path->pathFromProjectRoot(
            'docker/web/.bash_history',
        ));
        touch($this->path->pathFromProjectRoot(
            'docker/web/.env.local',
        ));
    }
}
