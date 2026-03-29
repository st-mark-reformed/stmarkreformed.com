<?php

declare(strict_types=1);

namespace App\Series;

use App\DropdownList\DropdownListEntity;
use App\DropdownList\DropdownListItems;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_find;
use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class SeriesCollection implements JsonSerializable
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

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
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

    /**
     * @param callable(Series): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->items,
        ));
    }

    public function asDropdownList(): DropdownListItems
    {
        return new DropdownListItems(items: $this->map(
            callback: static function (Series $series): DropdownListEntity {
                return new DropdownListEntity(
                    value: $series->id->toString(),
                    label: $series->title,
                );
            },
        ));
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
