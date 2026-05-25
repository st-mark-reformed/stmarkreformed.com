<?php

declare(strict_types=1);

namespace App\Series;

use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

interface Series
{
    public UuidInterface $id { get; }

    public string $title { get; }

    public SeriesSlug $slug { get; }

    public function isEmpty(): bool;

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     slug: string,
     * }
     */
    public function asArray(): array;
}
