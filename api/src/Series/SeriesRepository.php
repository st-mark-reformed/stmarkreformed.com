<?php

declare(strict_types=1);

namespace App\Series;

use App\Result\Result;
use App\Series\Persistence\CreateSeries;

readonly class SeriesRepository
{
    public function __construct(
        private CreateSeries $createSeries,
    ) {
    }

    public function create(NewSeries $series): Result
    {
        return $this->createSeries->create(series: $series);
    }
}
