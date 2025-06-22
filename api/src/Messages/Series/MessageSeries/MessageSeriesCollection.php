<?php

declare(strict_types=1);

namespace App\Messages\Series\MessageSeries;

use function array_filter;
use function array_map;
use function array_merge;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class MessageSeriesCollection
{
    /** @param MessageSeries[] $messageSeries */
    public function __construct(public array $messageSeries = [])
    {
        array_map(
            static fn (MessageSeries $s) => $s,
            $messageSeries,
        );
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->messageSeries);
    }

    /** @return array<array-key, array<scalar>> */
    public function asScalar(): array
    {
        return $this->mapToArray(
            static fn (MessageSeries $series) => $series->asScalar(),
        );
    }

    public function filter(callable $callback): MessageSeriesCollection
    {
        return new MessageSeriesCollection(array_filter(
            $this->messageSeries,
            $callback,
        ));
    }

    public function findFirst(): MessageSeries|null
    {
        return $this->messageSeries[0] ?? null;
    }

    public function findById(string $id): MessageSeries|null
    {
        return $this->filter(
            static fn (MessageSeries $p) => $p->id->toString() === $id,
        )->findFirst();
    }

    public function withAddedSeries(
        MessageSeries|null $series,
    ): MessageSeriesCollection {
        if ($series === null) {
            return $this;
        }

        return new MessageSeriesCollection(array_merge(
            $this->messageSeries,
            [$series],
        ));
    }

    public function withAddedSeriesCollection(
        MessageSeriesCollection $series,
    ): MessageSeriesCollection {
        return new MessageSeriesCollection(array_merge(
            $this->messageSeries,
            $series->messageSeries,
        ));
    }
}
