<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

readonly class MessageSeriesOrBy
{
    public function __construct(
        public string $title,
        public string $slug,
    ) {
    }
}
