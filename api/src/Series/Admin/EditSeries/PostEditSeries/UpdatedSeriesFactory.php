<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\PostEditSeries;

use App\Series\Series;
use App\Series\SeriesResult;

readonly class UpdatedSeriesFactory
{
    public function create(
        Series $requestSeries,
        SeriesResult $persistentSeriesResult,
    ): Series {
        if (! $persistentSeriesResult->hasSeries) {
            return $persistentSeriesResult->series;
        }

        return $persistentSeriesResult->series
            ->withTitle(value: $requestSeries->title)
            ->withSlug(value: $requestSeries->slug);
    }
}
