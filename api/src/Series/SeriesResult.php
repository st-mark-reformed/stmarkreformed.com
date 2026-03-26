<?php

declare(strict_types=1);

namespace App\Series;

readonly class SeriesResult
{
    public bool $hasSeries;

    public Series $series;

    public function __construct(Series|null $series = null)
    {
        $this->hasSeries = $series !== null;
        $this->series    = $series ?? new Series();
    }
}
