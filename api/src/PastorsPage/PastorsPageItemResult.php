<?php

declare(strict_types=1);

namespace App\PastorsPage;

readonly class PastorsPageItemResult
{
    public bool $hasPastorsPageItem;

    public PastorsPageItem $pastorsPageItem;

    public function __construct(PastorsPageItem|null $pastorsPageItem = null)
    {
        $this->hasPastorsPageItem = $pastorsPageItem !== null;
        $this->pastorsPageItem    = $pastorsPageItem ?? new PastorsPageItem();
    }
}
