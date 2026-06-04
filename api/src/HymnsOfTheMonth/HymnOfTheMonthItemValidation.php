<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

readonly class HymnOfTheMonthItemValidation
{
    /** @return string[] */
    public static function validate(
        NewHymnOfTheMonthItem|HymnOfTheMonthItem $hymnOfTheMonthItem,
    ): array {
        $messages = [];

        if ($hymnOfTheMonthItem->hymnPsalmName === '') {
            $messages[] = 'Hymn/Psalm name is required';
        }

        return $messages;
    }
}
