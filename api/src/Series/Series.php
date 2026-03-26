<?php

declare(strict_types=1);

namespace App\Series;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

readonly class Series
{
    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public string $title = '',
        public SeriesSlug $slug = new SeriesSlug(),
    ) {
    }

    /**
     * @return array{
     *     title: string,
     *     slug: string
     * }
     */
    public function asArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug->slug,
        ];
    }
}
