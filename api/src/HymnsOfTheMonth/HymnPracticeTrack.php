<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use JsonSerializable;

readonly class HymnPracticeTrack implements JsonSerializable
{
    public function __construct(
        public string $title = '',
        public string $path = '',
    ) {
    }

    /** @return array{title: string, path: string} */
    public function asArray(): array
    {
        return [
            'title' => $this->title,
            'path' => $this->path,
        ];
    }

    /** @return array{title: string, path: string} */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
