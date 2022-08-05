<?php

declare(strict_types=1);

namespace App\MailingLists;

use function array_map;
use function array_values;

class SubscriberCollection
{
    /** @var Subscriber[] */
    private array $items;

    /**
     * @param Subscriber[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static function (Subscriber $item): Subscriber {
                return $item;
            },
            $items,
        ));
    }

    /**
     * @return Subscriber[]
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
