<?php

declare(strict_types=1);

namespace App\Messages;

use App\Series\Series;

readonly class SeriesMessages
{
    public function __construct(
        public Series $series,
        public Messages $messages,
    ) {
    }
}
