<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use JsonSerializable;

use function array_filter;
use function array_map;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class HymnPracticeTracks implements JsonSerializable
{
    /** @var HymnPracticeTrack[] */
    public array $tracks;

    /** @param HymnPracticeTrack[] $tracks */
    public function __construct(array $tracks = [])
    {
        $this->tracks = array_values(array_map(
            static fn (HymnPracticeTrack $t) => $t,
            $tracks,
        ));
    }

    /**
     * Builds the collection from the raw `[{title, path}]` shape used by the
     * Craft transfer payload and the persisted JSON column.
     *
     * @param array<array-key, array{title?: string, path?: string}> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(tracks: array_values(array_map(
            static fn (array $track): HymnPracticeTrack => new HymnPracticeTrack(
                title: $track['title'] ?? '',
                path: $track['path'] ?? '',
            ),
            array_values(array_filter($raw, 'is_array')),
        )));
    }

    public function count(): int
    {
        return count($this->tracks);
    }

    /**
     * @param callable(HymnPracticeTrack): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->tracks,
        ));
    }

    /** @return array<array-key, array{title: string, path: string}> */
    public function asArray(): array
    {
        return array_map(
            static fn (HymnPracticeTrack $t) => $t->asArray(),
            $this->tracks,
        );
    }

    /** @return array<array-key, array{title: string, path: string}> */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
