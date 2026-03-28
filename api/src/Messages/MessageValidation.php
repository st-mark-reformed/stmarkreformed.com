<?php

declare(strict_types=1);

namespace App\Messages;

readonly class MessageValidation
{
    /** @return string[] */
    public static function validate(NewMessage|Message $message): array
    {
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
