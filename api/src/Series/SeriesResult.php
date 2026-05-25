<?php

declare(strict_types=1);

namespace App\Series;

readonly class SeriesResult
{
    public bool $hasSeries;

    public PopulatedSeries $series;

    public function __construct(PopulatedSeries|null $series = null)
    {
        $this->hasSeries = $series !== null;
        $this->series    = $series ?? new PopulatedSeries();
    }
}
