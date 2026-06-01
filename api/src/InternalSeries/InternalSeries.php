<?php

declare(strict_types=1);

namespace App\InternalSeries;

use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

interface InternalSeries
{
    public UuidInterface $id { get; }

    public string $title { get; }

    public InternalSeriesSlug $slug { get; }

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
