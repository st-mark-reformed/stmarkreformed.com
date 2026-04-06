<?php

declare(strict_types=1);

namespace App\Messages;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_find;
use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Messages implements JsonSerializable
{
    /** @var Message[] */
    public array $items;

    /** @param Message[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (Message $m) => $m,
            $items,
        ));
    }

    /**
     * @param callable(Message): T $callback
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

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array<array-key, array{
     *     isValid: bool,
     *      date: string,
     *      title: string,
     *      slug: string,
     *      audioPath: string,
     *      speaker: array{
     *          id: string,
     *          titleOrHonorific: string,
     *          firstName: string,
     *          lastName: string,
     *          fullName: string,
     *          fullNameWithHonorific: string,
     *          email: string,
     *          leadershipPosition: string,
     *          leadershipPositionHumanReadable: string,
     *          bio: string,
     *          hasMessages: bool,
     *      },
     *      passage: string,
     *      series: array{
     *          id: string,
     *          title: string,
     *          slug: string,
     *      },
     *      description: string,
     * }>
     */
    public function asArray(): array
    {
        /** @phpstan-ignore-next-line */
        return array_map(
            static fn (Message $i) => $i->asArray(),
            $this->items,
        );
    }

    public function findById(UuidInterface|string $id): Message|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->items,
            static fn (Message $message) => $message->id->toString() === $id,
        );
    }
}
