<?php

declare(strict_types=1);

namespace App\InternalMessages;

readonly class InternalMessageValidation
{
    /** @return string[] */
    public static function validate(
        NewInternalMessage|InternalMessage $message,
    ): array {
        $messages = [];

        if ($message->title === '') {
            $messages[] = 'Title is required';
        }

        if ($message->audioPath === '') {
            $messages[] = 'Audio is required';
        }

        return $messages;
    }
}
