<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use function array_map;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class MessageRecords
{
    /** @param MessageRecord[] $records */
    public function __construct(public array $records = [])
    {
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->records);
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->records);
    }

    /** @phpstan-ignore-next-line */
    public function asScalar(): array
    {
        return $this->mapToArray(
            static fn (MessageRecord $record) => $record->asScalar(),
        );
    }

    public function count(): int
    {
        return count($this->records);
    }
}
