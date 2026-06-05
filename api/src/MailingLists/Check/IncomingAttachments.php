<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use function array_map;
use function array_values;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class IncomingAttachments
{
    /** @var IncomingAttachment[] */
    public array $items;

    /** @param IncomingAttachment[] $items */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static fn (IncomingAttachment $a) => $a,
            $items,
        ));
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param callable(IncomingAttachment): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map($callback, $this->items));
    }
}
