<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\GetEditMessage;

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

        return $persistentMessageResult->message
            ->withEnabled(value: $requestMessage->isEnabled)
            ->withSpeaker(value: $requestMessage->speaker)
            ->withDate(value: $requestMessage->date)
            ->withTitle(value: $requestMessage->title)
            ->withAudioPath(value: $requestMessage->audioPath)
            ->withPassage(value: $requestMessage->passage)
            ->withSeries(value: $requestMessage->series);
    }
}
