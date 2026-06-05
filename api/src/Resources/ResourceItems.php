<?php

declare(strict_types=1);

namespace App\Resources;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_find;
use function array_map;
use function array_slice;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class ResourceItems implements JsonSerializable
{
    /** @var ResourceItem[] */
    public array $items;

    /** @param ResourceItem[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (ResourceItem $i) => $i,
            $items,
        ));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function sliceToPage(int $page, int $perPage): ResourceItems
    {
        return new self(items: array_slice(
            $this->items,
            ($page * $perPage) - $perPage,
            $perPage,
        ));
    }

    /**
     * @param callable(ResourceItem): T $callback
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

    /** @param callable(ResourceItem): bool $callback */
    public function filter(callable $callback): ResourceItems
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
     *     body: string,
     *     downloads: array<array-key, array{filename: string}>,
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (ResourceItem $i) => $i->asArray(),
            $this->items,
        );
    }

    public function findById(UuidInterface|string $id): ResourceItem|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->items,
            static fn (ResourceItem $resourceItem) => $resourceItem->id->toString() === $id,
        );
    }
}
