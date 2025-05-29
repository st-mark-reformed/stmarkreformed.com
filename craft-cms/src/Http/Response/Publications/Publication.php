<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use DateTimeInterface;

class Publication
{
    public function __construct(
        public string $title,
        public string $slug,
        public string $url,
        public string $bodyHtml,
        public DateTimeInterface $publicationDate,
        public string $uid,
    ) {
    }
}
