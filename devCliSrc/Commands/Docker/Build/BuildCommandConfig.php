<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Build;

use Cli\Shared\DockerImage;

use function array_filter;
use function array_map;

readonly class BuildCommandConfig
{
    /** @var DockerImage[] */
    public array $images;

    /** @param array<array-key, string|DockerImage>|null $images */
    public function __construct(array|null $images = null)
    {
        $this->images = DockerImage::fromArray($images);
    }

    public function filter(callable $callback): BuildCommandConfig
    {
        return new self(array_filter(
            $this->images,
            $callback,
        ));
    }

    public function walkImages(callable $callback): void
    {
        array_map($callback, $this->images);
    }
}
