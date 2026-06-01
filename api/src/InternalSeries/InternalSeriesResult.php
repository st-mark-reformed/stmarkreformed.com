<?php

declare(strict_types=1);

namespace App\InternalSeries;

readonly class InternalSeriesResult
{
    public bool $hasSeries;

    public PopulatedInternalSeries $series;

    public function __construct(PopulatedInternalSeries|null $series = null)
    {
        $this->hasSeries = $series !== null;
        $this->series    = $series ?? new PopulatedInternalSeries();
    }
}
