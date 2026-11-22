<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Build;

use Cli\Shared\DockerImage;

use function array_filter;
use function array_map;
use function in_array;

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

    /**
     * Adds the images built FROM these images, in DockerImage case order so
     * each parent builds before its dependents
     */
    public function withDependents(): BuildCommandConfig
    {
        return new self(array_filter(
            DockerImage::cases(),
            fn (DockerImage $image): bool => in_array(
                $image,
                $this->images,
                true,
            ) || in_array($image->parent(), $this->images, true),
        ));
    }

    public function walkImages(callable $callback): void
    {
        array_map($callback, $this->images);
    }
}
