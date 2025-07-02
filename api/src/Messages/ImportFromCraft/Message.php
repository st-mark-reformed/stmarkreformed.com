<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

readonly class Message
{
    public function __construct(
        public string $uid,
        public string $title,
        public string $slug,
        public string $postDate,
        public MessageSeriesOrBy|null $by,
        public string $text,
        public MessageSeriesOrBy|null $series,
        public string $audioFileName,
    ) {
    }
}
