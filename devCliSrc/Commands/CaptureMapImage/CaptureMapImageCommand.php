<?php

declare(strict_types=1);

namespace Cli\Commands\CaptureMapImage;

use Cli\CliQuestion;
use Cli\Commands\Docker\Container\ContainerCommand;
use Cli\Commands\Docker\Container\ContainerConfig;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function escapeshellarg;
use function implode;
use function trim;

readonly class CaptureMapImageCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            implode(' ', [
                'map-image:capture',
                '[--street-address=]',
                '[--output=]',
            ]),
            self::class,
        )->descriptions(
            'Captures a fresh Google Maps screenshot for the home page (use --help to see arguments)',
            [
                '--street-address' => 'The street address Google Maps should center on',
                '--output' => 'The output filename, written into web/public/images/home/',
            ],
        );
    }

    public function __construct(
        private CliQuestion $cliQuestion,
        private ContainerCommand $containerCommand,
    ) {
    }

    public function __invoke(
        string|null $streetAddress = null,
        string|null $output = null,
    ): void {
        $streetAddress = $this->resolve(
            $streetAddress,
            'Street address to center the map on: ',
        );

        $output = $this->resolve(
            $output,
            'Output filename (e.g. fcs-map-image.png): ',
        );

        $nodeCommand = implode(' ', [
            'cd /app &&',
            'node --experimental-strip-types mapImageCapture/captureMapImage.ts',
            '--street-address ' . escapeshellarg($streetAddress),
            '--output ' . escapeshellarg($output),
        ]);

        $this->containerCommand->exec(
            'web',
            new ContainerConfig($nodeCommand),
        );
    }

    private function resolve(string|null $value, string $prompt): string
    {
        if ($value !== null && trim($value) !== '') {
            return $value;
        }

        return $this->cliQuestion->ask($prompt, required: true);
    }
}
