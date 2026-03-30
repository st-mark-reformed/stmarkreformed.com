<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\PostEditMessage;

use App\Messages\Message;
use App\Messages\MessageResult;

readonly class UpdatedMessageFactory
{
    public function create(
        Message $requestMessage,
        MessageResult $persistentMessageResult,
    ): Message {
        if (! $persistentMessageResult->hasMessage) {
            return $persistentMessageResult->message;
        }

        $message = $persistentMessageResult->message
            ->withEnabled(value: $requestMessage->isEnabled)
            ->withSpeaker(value: $requestMessage->speaker)
            ->withDate(value: $requestMessage->date)
            ->withTitle(value: $requestMessage->title)
            ->withPassage(value: $requestMessage->passage)
            ->withSeries(value: $requestMessage->series);

        if ($requestMessage->audioPath !== '') {
            $message = $message->withAudioPath(
                value: $requestMessage->audioPath,
            );
        }

        return $message;
    }
}
