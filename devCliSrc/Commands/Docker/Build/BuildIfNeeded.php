<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Build;

use Cli\CliSrcPath;
use Cli\Shared\DockerImage;

use function file_get_contents;
use function md5_file;
use function shell_exec;
use function trim;

readonly class BuildIfNeeded
{
    public function __construct(
        private CliSrcPath $cliSrcPath,
        private BuildCommand $buildCommand,
    ) {
    }

    public function run(): void
    {
        $buildCommandConfig = new BuildCommandConfig()->filter(
            function (DockerImage $image): bool {
                $hash = md5_file($this->cliSrcPath->pathFromProjectRoot(
                    $image->dockerfilePath(),
                ));

                $previousHash = trim((string) file_get_contents(
                    $this->cliSrcPath->dockerfileHashesPath(
                        $image->name,
                    ),
                ));

                if ($hash !== $previousHash) {
                    return true;
                }

                $tagCheck = trim((string) shell_exec(
                    'docker images -q ' . $image->tag(),
                ));

                return $tagCheck === '';
            },
        );

        $this->buildCommand->run($buildCommandConfig);
    }
}
