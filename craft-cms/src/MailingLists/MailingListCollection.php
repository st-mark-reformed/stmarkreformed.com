<?php

declare(strict_types=1);

namespace App\MailingLists;

use function array_map;
use function array_values;

class MailingListCollection
{
    /** @var MailingList[] */
    private array $items;

    /**
     * @param MailingList[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static function (MailingList $item): MailingList {
                return $item;
            },
            $items,
        ));
    }

    /**
     * @return MailingList[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map($callable, $this->items());
    }
}
