<?php

declare(strict_types=1);

namespace App\Messages\Message;

use function array_map;
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

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->messages);
    }

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
