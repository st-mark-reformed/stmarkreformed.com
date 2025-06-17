<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use function array_map;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class MessageSeriesRecordCollection
{
    /** @param MessageSeriesRecord[] $records */
    public function __construct(public array $records = [])
    {
        array_map(
            static fn (MessageSeriesRecord $r) => $r,
            $records,
        );
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->records);
    }
}
