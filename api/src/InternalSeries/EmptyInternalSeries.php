<?php

declare(strict_types=1);

namespace App\InternalSeries;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class EmptyInternalSeries implements InternalSeries, JsonSerializable
{
    public UuidInterface $id;

    public string $title;

    public InternalSeriesSlug $slug;

    public function __construct()
    {
        $this->id    = new EmptyUuid();
        $this->title = '';
        $this->slug  = new InternalSeriesSlug();
    }

    public function isEmpty(): bool
    {
        return true;
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
