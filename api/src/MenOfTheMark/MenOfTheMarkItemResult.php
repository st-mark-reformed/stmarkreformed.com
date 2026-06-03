<?php

declare(strict_types=1);

namespace App\MenOfTheMark;

readonly class MenOfTheMarkItemResult
{
    public bool $hasMenOfTheMarkItem;

    public MenOfTheMarkItem $menOfTheMarkItem;

    public function __construct(MenOfTheMarkItem|null $menOfTheMarkItem = null)
    {
        $this->hasMenOfTheMarkItem = $menOfTheMarkItem !== null;
        $this->menOfTheMarkItem    = $menOfTheMarkItem ?? new MenOfTheMarkItem();
    }
}
