<?php

declare(strict_types=1);

namespace App\Messages\Series\MessageSeries;

use function array_map;

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
}
