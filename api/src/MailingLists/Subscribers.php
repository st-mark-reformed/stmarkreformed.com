<?php

declare(strict_types=1);

namespace App\MailingLists;

use JsonSerializable;

use function array_filter;
use function array_find;
use function array_map;
use function array_values;
use function count;
use function strtolower;
use function trim;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Subscribers implements JsonSerializable
{
    /** @var Subscriber[] */
    public array $items;

    /** @param Subscriber[] $items */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static fn (Subscriber $s) => $s,
            $items,
        ));
    }

    /**
     * Builds the collection from the raw `[{name, emailAddress}]` shape used by
     * the admin form payload.
     *
     * @param array<array-key, array{name?: string, emailAddress?: string}> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(items: array_values(array_map(
            static fn (array $subscriber): Subscriber => new Subscriber(
                name: $subscriber['name'] ?? '',
                emailAddress: $subscriber['emailAddress'] ?? '',
            ),
            array_values(array_filter($raw, 'is_array')),
        )));
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param callable(Subscriber): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map($callback, $this->items));
    }

    /** @param callable(Subscriber): bool $callback */
    public function filter(callable $callback): Subscribers
    {
        return new self(items: array_values(array_filter(
            $this->items,
            $callback,
        )));
    }

    public function hasEmailAddress(string $emailAddress): bool
    {
        $needle = strtolower(trim($emailAddress));

        return array_find(
            $this->items,
            static fn (Subscriber $s) => strtolower(trim($s->emailAddress)) === $needle,
        ) !== null;
    }

    /** @return array<array-key, array{id: string, name: string, emailAddress: string}> */
    public function asArray(): array
    {
        return array_map(
            static fn (Subscriber $s) => $s->asArray(),
            $this->items,
        );
    }

    /** @return array<array-key, array{id: string, name: string, emailAddress: string}> */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
