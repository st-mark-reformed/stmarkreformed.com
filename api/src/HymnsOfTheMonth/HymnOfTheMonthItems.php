<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_find;
use function array_map;
use function array_slice;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class HymnOfTheMonthItems implements JsonSerializable
{
    /** @var HymnOfTheMonthItem[] */
    public array $items;

    /** @param HymnOfTheMonthItem[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (HymnOfTheMonthItem $i) => $i,
            $items,
        ));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function sliceToPage(int $page, int $perPage): HymnOfTheMonthItems
    {
        return new self(items: array_slice(
            $this->items,
            ($page * $perPage) - $perPage,
            $perPage,
        ));
    }

    /**
     * @param callable(HymnOfTheMonthItem): T $callback
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

    /** @param callable(HymnOfTheMonthItem): bool $callback */
    public function filter(callable $callback): HymnOfTheMonthItems
    {
        return new self(items: array_values(array_filter(
            $this->items,
            $callback,
        )));
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array<array-key, array{
     *     id: string,
     *     isEnabled: bool,
     *     date: string,
     *     title: string,
     *     slug: string,
     *     hymnPsalmName: string,
     *     musicSheetPath: string,
     *     practiceTracks: array<array-key, array{title: string, path: string}>,
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (HymnOfTheMonthItem $i) => $i->asArray(),
            $this->items,
        );
    }

    public function findById(UuidInterface|string $id): HymnOfTheMonthItem|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->items,
            static fn (HymnOfTheMonthItem $item) => $item->id->toString() === $id,
        );
    }
}
