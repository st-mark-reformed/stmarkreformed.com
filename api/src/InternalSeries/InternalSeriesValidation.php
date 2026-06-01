<?php

declare(strict_types=1);

namespace App\InternalSeries;

readonly class InternalSeriesValidation
{
    /** @return string[] */
    public static function validate(
        NewInternalSeries|PopulatedInternalSeries $series,
    ): array {
        $messages = [];

        if ($series->title === '') {
            $messages[] = 'Title is required';
        }

        if (! $series->slug->isValid) {
            $messages[] = $series->slug->validationMessage;
        }

        return $messages;
    }
}
