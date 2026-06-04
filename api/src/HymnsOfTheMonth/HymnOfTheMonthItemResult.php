<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

readonly class HymnOfTheMonthItemResult
{
    public bool $hasHymnOfTheMonthItem;

    public HymnOfTheMonthItem $hymnOfTheMonthItem;

    public function __construct(HymnOfTheMonthItem|null $hymnOfTheMonthItem = null)
    {
        $this->hasHymnOfTheMonthItem = $hymnOfTheMonthItem !== null;
        $this->hymnOfTheMonthItem    = $hymnOfTheMonthItem ?? new HymnOfTheMonthItem();
    }
}
