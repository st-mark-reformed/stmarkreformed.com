<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\EditInternalMessage\PostEditInternalMessage;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessageResult;

readonly class UpdatedInternalMessageFactory
{
    public function create(
        InternalMessage $requestMessage,
        InternalMessageResult $persistentMessageResult,
    ): InternalMessage {
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
