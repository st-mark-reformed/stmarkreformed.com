<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use function array_map;

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
}
