<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\EditInternalSeries\PostEditInternalSeries;

use App\InternalSeries\InternalSeriesResult;
use App\InternalSeries\PopulatedInternalSeries;

readonly class UpdatedInternalSeriesFactory
{
    public function create(
        PopulatedInternalSeries $requestSeries,
        InternalSeriesResult $persistentSeriesResult,
    ): PopulatedInternalSeries {
        if (! $persistentSeriesResult->hasSeries) {
            return $persistentSeriesResult->series;
        }

        return $persistentSeriesResult->series
            ->withTitle(value: $requestSeries->title)
            ->withSlug(value: $requestSeries->slug);
    }
}
