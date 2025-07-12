<?php

declare(strict_types=1);

namespace App\Messages\Message;

use function array_filter;
use function array_map;
use function array_slice;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Messages
{
    /** @param Message[] $messages */
    public function __construct(public array $messages = [])
    {
        array_map(
            static fn (Message $m) => $m,
            $messages,
        );
    }

    public function filter(callable $callback): Messages
    {
        return new Messages(array_values(array_filter(
            $this->messages,
            $callback,
        )));
    }

    public function slice(
        int $offset,
        int $limit,
    ): Messages {
        return new Messages(array_values(array_slice(
            $this->messages,
            $offset,
            $limit,
        )));
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->messages);
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->messages);
    }

    /** @phpstan-ignore-next-line */
    public function asScalar(): array
    {
        return $this->mapToArray(
            static fn (Message $message) => $message->asScalar(),
        );
    }

    public function count(): int
    {
        return count($this->messages);
    }
}
