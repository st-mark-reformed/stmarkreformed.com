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

        // Only override the stored audio when a new file was actually uploaded;
        // an unchanged edit resubmits the existing path and must keep its size.
        if ($requestMessage->audioPathIsFileUpload()) {
            $message = $message
                ->withAudioPath(value: $requestMessage->audioPath)
                ->withAudioFileSize(value: $requestMessage->audioFileSize);
        }

        return $message;
    }
}
