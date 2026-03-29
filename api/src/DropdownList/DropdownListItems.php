<?php

declare(strict_types=1);

namespace App\DropdownList;

use JsonSerializable;

use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class DropdownListItems implements JsonSerializable
{
    /** @var DropdownListEntity[] */
    public array $items;

    /** @param DropdownListEntity[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (DropdownListEntity $i) => $i,
            $items,
        ));
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array<array-key, array{
     *     value: string,
     *     label: string
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (DropdownListEntity $item) => $item->asArray(),
            $this->items,
        );
    }
}
