<?php

declare(strict_types=1);

namespace App\MailingLists;

use PhpImap\IncomingMail;

use function array_map;
use function array_values;

class IncomingMailCollection
{
    /** @var IncomingMail[] */
    private array $items;

    /**
     * @param IncomingMail[] $items
     */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static function (IncomingMail $item): IncomingMail {
                return $item;
            },
            $items,
        ));
    }

    /**
     * @return IncomingMail[]
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
