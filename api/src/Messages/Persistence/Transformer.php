<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message\Message;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class Transformer
{
    public function createRecord(Message $fromMessage): MessageRecord
    {
        $record = new MessageRecord();

        $record->id = $fromMessage->id->toString();

        $record->is_published = $fromMessage->isPublished;

        $record->date = $fromMessage->date?->format('Y-m-d H:i:s');

        $record->title = $fromMessage->title->title;

        $record->text = $fromMessage->text;

        $record->speaker_profile_id = $fromMessage->speaker?->id->toString();

        $record->series_id = $fromMessage->series?->id->toString();

        $record->audio_file_name = $fromMessage->audioFileName->audioFileName;

        return $record;
    }
}
