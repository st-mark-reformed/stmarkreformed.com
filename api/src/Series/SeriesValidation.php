<?php

declare(strict_types=1);

namespace App\Series;

readonly class SeriesValidation
{
    /** @return string[] */
    public static function validate(NewSeries|Series $series): array
    {
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
