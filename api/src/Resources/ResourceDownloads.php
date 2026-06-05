<?php

declare(strict_types=1);

namespace App\Resources;

use JsonSerializable;

use function array_filter;
use function array_map;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class ResourceDownloads implements JsonSerializable
{
    /** @var ResourceDownload[] */
    public array $downloads;

    /** @param ResourceDownload[] $downloads */
    public function __construct(array $downloads = [])
    {
        $this->downloads = array_values(array_map(
            static fn (ResourceDownload $d) => $d,
            $downloads,
        ));
    }

    /**
     * Builds the collection from the raw `[{filename}]` shape used by the Craft
     * transfer payload and the persisted JSON column.
     *
     * @param array<array-key, array{filename?: string}> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(downloads: array_values(array_map(
            static fn (array $download): ResourceDownload => new ResourceDownload(
                filename: $download['filename'] ?? '',
            ),
            array_values(array_filter($raw, 'is_array')),
        )));
    }

    public function count(): int
    {
        return count($this->downloads);
    }

    /**
     * @param callable(ResourceDownload): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->downloads,
        ));
    }

    /** @return array<array-key, array{filename: string}> */
    public function asArray(): array
    {
        return array_map(
            static fn (ResourceDownload $d) => $d->asArray(),
            $this->downloads,
        );
    }

    /** @return array<array-key, array{filename: string}> */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
