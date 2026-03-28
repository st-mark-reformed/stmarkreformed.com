<?php

declare(strict_types=1);

namespace App\Series;

use Ramsey\Uuid\UuidInterface;

use function array_find;
use function array_map;
use function array_values;

readonly class SeriesCollection
{
    /** @var Series[] */
    public array $items;

    /** @param Series[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (Series $s) => $s,
            $items,
        ));
    }

    /**
     * @return array<array-key, array{
     *     id: string,
     *     title: string,
     *     slug: string,
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (Series $i) => $i->asArray(),
            $this->items,
        );
    }

    public function findById(UuidInterface|string $id): Series|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->items,
            static fn (Series $series) => $series->id->toString() === $id,
        );
    }
}
