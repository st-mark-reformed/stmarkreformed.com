<?php

declare(strict_types=1);

namespace App\PastorsPage;

readonly class PastorsPageItemValidation
{
    /** @return string[] */
    public static function validate(
        NewPastorsPageItem|PastorsPageItem $pastorsPageItem,
    ): array {
        $messages = [];

        if ($pastorsPageItem->title === '') {
            $messages[] = 'Title is required';
        }

        if ($pastorsPageItem->body === '') {
            $messages[] = 'Body is required';
        }

        return $messages;
    }
}
