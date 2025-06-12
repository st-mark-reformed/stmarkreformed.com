<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use function array_map;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class UserRecords
{
    /** @param UserRecord[] $records */
    public function __construct(public array $records = [])
    {
        array_map(static fn (UserRecord $r) => $r, $records);
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->records);
    }
}
