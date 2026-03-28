<?php

declare(strict_types=1);

namespace App\Series;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Series implements JsonSerializable
{
    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public string $title = '',
        public SeriesSlug $slug = new SeriesSlug(),
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     slug: string,
     * }
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'title' => $this->title,
            'slug' => $this->slug->toString(),
        ];
    }
}
