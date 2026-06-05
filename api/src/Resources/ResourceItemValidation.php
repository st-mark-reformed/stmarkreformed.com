<?php

declare(strict_types=1);

namespace App\Resources;

readonly class ResourceItemValidation
{
    /** @return string[] */
    public static function validate(NewResourceItem|ResourceItem $resourceItem): array
    {
        $messages = [];

        if ($resourceItem->title === '') {
            $messages[] = 'Title is required';
        }

        return $messages;
    }
}
