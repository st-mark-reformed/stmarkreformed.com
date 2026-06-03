<?php

declare(strict_types=1);

namespace App\MenOfTheMark;

readonly class MenOfTheMarkItemValidation
{
    /** @return string[] */
    public static function validate(
        NewMenOfTheMarkItem|MenOfTheMarkItem $menOfTheMarkItem,
    ): array {
        $messages = [];

        if ($menOfTheMarkItem->title === '') {
            $messages[] = 'Title is required';
        }

        if ($menOfTheMarkItem->body === '') {
            $messages[] = 'Body is required';
        }

        return $messages;
    }
}
