<?php

declare(strict_types=1);

namespace App\Messages;

use App\Profiles\Profile;
use App\Series\Series;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_find;
use function array_map;
use function array_slice;
use function array_values;
use function count;

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

    public function count(): int
    {
        return count($this->items);
    }

    public function sliceToPage(int $page, int $perPage): Messages
    {
        return new self(items: array_slice(
            $this->items,
            ($page * $perPage) - $perPage,
            $perPage,
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

    /** @param callable(Message): bool $callback */
    public function filter(callable $callback): Messages
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

    /**
     * Speakers that appear on any message in this collection. Empty-slugged
     * speakers (i.e. messages without an associated profile) are excluded.
     * Order matches the first-occurrence order within the collection.
     *
     * @return Profile[]
     */
    public function distinctSpeakers(): array
    {
        $seen = [];

        foreach ($this->items as $message) {
            if ($message->speaker->slug === '') {
                continue;
            }

            $id = $message->speaker->id->toString();

            if (isset($seen[$id])) {
                continue;
            }

            $seen[$id] = $message->speaker;
        }

        return array_values($seen);
    }

    /**
     * Series that appear on any message in this collection. Empty-slugged
     * series are excluded. Order matches the first-occurrence order within
     * the collection.
     *
     * @return Series[]
     */
    public function distinctSeries(): array
    {
        $seen = [];

        foreach ($this->items as $message) {
            if ($message->series->slug->toString() === '') {
                continue;
            }

            $id = $message->series->id->toString();

            if (isset($seen[$id])) {
                continue;
            }

            $seen[$id] = $message->series;
        }

        return array_values($seen);
    }

    public function bySpeakerId(UuidInterface|string $id): Messages
    {
        $idString = $id instanceof UuidInterface ? $id->toString() : $id;

        return $this->filter(
            callback: static fn (Message $m): bool => $m->speaker->id->toString() === $idString,
        );
    }

    public function bySeriesId(UuidInterface|string $id): Messages
    {
        $idString = $id instanceof UuidInterface ? $id->toString() : $id;

        return $this->filter(
            callback: static fn (Message $m): bool => $m->series->id->toString() === $idString,
        );
    }
}
