<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Build;

use Cli\CliSrcPath;
use Cli\Shared\DockerImage;
use Cli\Shared\SplitMultipleArgumentValues;
use Cli\StreamCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function array_map;
use function count;
use function file_put_contents;
use function implode;
use function is_dir;
use function md5_file;
use function mkdir;

readonly class BuildCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'docker:build [-i|--image=]*',
            self::class,
        )->descriptions(
            'Builds Docker images (use --help to see arguments)',
            [
                '--image' => implode('', [
                    'Specify which image(s) to build (',
                    implode('|', array_map(
                        static fn (DockerImage $i) => $i->name,
                        DockerImage::cases(),
                    )),
                    ')',
                ]),
            ],
        );
    }

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
        private SplitMultipleArgumentValues $splitMultipleArgumentValues,
    ) {
    }

    /** @param string[] $image */
    public function __invoke(array $image): void
    {
        $image = $this->splitMultipleArgumentValues->split($image);

        $this->run(new BuildCommandConfig(
            count($image) < 1 ? null : $image,
        ));
    }

    public function run(
        BuildCommandConfig $config = new BuildCommandConfig(),
    ): void {
        $config->walkImages(function (DockerImage $image): void {
            $this->output->writeln(
                '<fg=cyan>Building Docker image: ' . $image->name . ' </>',
            );

            $this->streamCommand->stream([
                'docker',
                'build',
                '--build-arg',
                'BUILDKIT_INLINE_CACHE=1',
                '--cache-from',
                $image->tag(),
                '--file',
                $image->dockerfilePath(),
                '--tag',
                $image->tag(),
                '.',
            ]);

            $this->writeFileHash(
                $image->name,
                $image->dockerfilePath(),
            );

            $this->output->writeln(
                '<fg=green>Finished building image: ' . $image->name . ' </>',
            );
        });
    }

    private function writeFileHash(string $name, string $dockerfilePath): void
    {
        $hash = md5_file($this->cliSrcPath->pathFromProjectRoot(
            $dockerfilePath,
        ));

        if (! is_dir($this->cliSrcPath->dockerfileHashesPath())) {
            mkdir(
                $this->cliSrcPath->dockerfileHashesPath(),
                0777,
                true,
            );
        }

        file_put_contents(
            $this->cliSrcPath->dockerfileHashesPath($name),
            $hash,
        );
    }
}
