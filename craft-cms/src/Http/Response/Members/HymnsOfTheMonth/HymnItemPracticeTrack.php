<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

class HymnItemPracticeTrack
{
    public function __construct(
        public string $title,
        public string $path,
    ) {
    }
}
