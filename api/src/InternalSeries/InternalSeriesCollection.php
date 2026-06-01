<?php

declare(strict_types=1);

namespace App\InternalSeries;

use App\DropdownList\DropdownListEntity;
use App\DropdownList\DropdownListItems;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class InternalSeriesCollection implements JsonSerializable
{
    /** @var InternalSeries[] */
    public array $items;

    /** @var array<string, InternalSeries> */
    private array $byId;

    /** @param InternalSeries[] $items */
    public function __construct(array $items)
    {
        $deduped = array_values(array_map(
            static fn (InternalSeries $s) => $s,
            $items,
        ));

        $byId = [];

        foreach ($deduped as $series) {
            $byId[$series->id->toString()] = $series;
        }

        $this->items = $deduped;
        $this->byId  = $byId;
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
            static fn (InternalSeries $i) => $i->asArray(),
            $this->items,
        );
    }

    /**
     * @param callable(InternalSeries): T $callback
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
            callback: static function (InternalSeries $series): DropdownListEntity {
                return new DropdownListEntity(
                    value: $series->id->toString(),
                    label: $series->title,
                );
            },
        ));
    }

    public function findById(UuidInterface|string $id): InternalSeries|null
    {
        $idString = $id instanceof UuidInterface ? $id->toString() : $id;

        return $this->byId[$idString] ?? null;
    }
}
