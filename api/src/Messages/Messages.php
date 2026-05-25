<?php

declare(strict_types=1);

namespace App\Messages;

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
     * Group messages by their speaker in a single pass. Messages whose
     * speaker is empty are excluded. Groups are returned in first-occurrence
     * order of the speaker within the collection. Within each group, the
     * messages preserve their original order.
     *
     * @return SpeakerMessages[]
     */
    public function groupBySpeaker(): array
    {
        $speakers = [];
        $bucket   = [];

        foreach ($this->items as $message) {
            if ($message->speaker->isEmpty()) {
                continue;
            }

            $id = $message->speaker->id->toString();

            if (! isset($bucket[$id])) {
                $bucket[$id]   = [];
                $speakers[$id] = $message->speaker;
            }

            $bucket[$id][] = $message;
        }

        $result = [];

        foreach ($bucket as $id => $items) {
            $result[] = new SpeakerMessages(
                speaker: $speakers[$id],
                messages: new self(items: $items),
            );
        }

        return $result;
    }

    /**
     * Group messages by their series in a single pass. Messages whose series
     * is empty are excluded. Groups are returned in first-occurrence order of
     * the series within the collection. Within each group, the messages
     * preserve their original order.
     *
     * @return SeriesMessages[]
     */
    public function groupBySeries(): array
    {
        $seriesById = [];
        $bucket     = [];

        foreach ($this->items as $message) {
            if ($message->series->isEmpty()) {
                continue;
            }

            $id = $message->series->id->toString();

            if (! isset($bucket[$id])) {
                $bucket[$id]     = [];
                $seriesById[$id] = $message->series;
            }

            $bucket[$id][] = $message;
        }

        $result = [];

        foreach ($bucket as $id => $items) {
            $result[] = new SeriesMessages(
                series: $seriesById[$id],
                messages: new self(items: $items),
            );
        }

        return $result;
    }
}
