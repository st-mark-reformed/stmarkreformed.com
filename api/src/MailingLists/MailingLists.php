<?php

declare(strict_types=1);

namespace App\MailingLists;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_find;
use function array_map;
use function array_slice;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class MailingLists implements JsonSerializable
{
    /** @var MailingList[] */
    public array $items;

    /** @param MailingList[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (MailingList $i) => $i,
            $items,
        ));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function sliceToPage(int $page, int $perPage): MailingLists
    {
        return new self(items: array_slice(
            $this->items,
            ($page * $perPage) - $perPage,
            $perPage,
        ));
    }

    /**
     * @param callable(MailingList): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map($callback, $this->items));
    }

    /** @param callable(MailingList): bool $callback */
    public function filter(callable $callback): MailingLists
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
     * Always serialized without passwords; the list view never exposes them.
     *
     * @phpstan-ignore-next-line
     */
    public function asArray(): array
    {
        return array_map(
            static fn (MailingList $i) => $i->asArrayWithoutPassword(),
            $this->items,
        );
    }

    public function findById(UuidInterface|string $id): MailingList|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->items,
            static fn (MailingList $mailingList) => $mailingList->id->toString() === $id,
        );
    }
}
