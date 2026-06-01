<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\InternalSeries\InternalSeries;

readonly class SeriesInternalMessages
{
    public function __construct(
        public InternalSeries $series,
        public InternalMessages $messages,
    ) {
    }
}
