<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message\AudioFileName;
use App\Messages\Message\Message;
use App\Messages\Message\Messages;
use App\Messages\Message\Slug;
use App\Messages\Message\Title;
use App\Messages\Series\MessageSeries\MessageSeriesCollection;
use App\Messages\Series\MessageSeriesRepository;
use App\Profiles\Profile\Profiles;
use App\Profiles\ProfileRepository;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

use function array_filter;
use function array_unique;
use function array_values;
use function count;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class Transformer
{
    private Profiles $possibleSpeakers;

    private MessageSeriesCollection $possibleSeries;

    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly MessageSeriesRepository $messageSeriesRepository,
    ) {
        $this->possibleSpeakers = new Profiles();

        $this->possibleSeries = new MessageSeriesCollection();
    }

    public function createRecord(Message $fromMessage): MessageRecord
    {
        $record = new MessageRecord();

        $record->id = $fromMessage->id->toString();

        $record->is_published = $fromMessage->isPublished;

        $record->date = $fromMessage->date?->format('Y-m-d H:i:s');

        $record->title = $fromMessage->title->title;

        $record->slug = $fromMessage->slug->slug;

        $record->text = $fromMessage->text;

        $record->speaker_profile_id = $fromMessage->speaker?->id->toString();

        $record->series_id = $fromMessage->series?->id->toString();

        $record->audio_file_name = $fromMessage->audioFileName->audioFileName;

        return $record;
    }

    public function createMessage(MessageRecord $fromRecord): Message
    {
        $date = null;

        if ($fromRecord->date !== null) {
            try {
                $date = DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $fromRecord->date,
                    new DateTimeZone('UTC'),
                );

                $date = $date instanceof DateTimeImmutable ? $date : null;
            } catch (Throwable) {
            }
        }

        $speaker = null;

        if ($fromRecord->speaker_profile_id !== null) {
            $speaker = $this->possibleSpeakers->findById(
                $fromRecord->speaker_profile_id,
            );

            if ($speaker === null) {
                $this->possibleSpeakers = $this->possibleSpeakers->withAddedProfile(
                    $this->profileRepository->findById(
                        $fromRecord->speaker_profile_id,
                    ),
                );
            }

            $speaker = $this->possibleSpeakers->findById(
                $fromRecord->speaker_profile_id,
            );
        }

        $series = null;

        if ($fromRecord->series_id !== null) {
            $series = $this->possibleSeries->findById(
                $fromRecord->series_id,
            );

            if ($series === null) {
                $this->possibleSeries = $this->possibleSeries->withAddedSeries(
                    $this->messageSeriesRepository->findById(
                        $fromRecord->series_id,
                    ),
                );
            }

            $series = $this->possibleSeries->findById(
                $fromRecord->series_id,
            );
        }

        return new Message(
            isPublished: $fromRecord->is_published,
            date: $date,
            title: new Title($fromRecord->title),
            slug: new Slug($fromRecord->slug),
            text: $fromRecord->text,
            speaker: $speaker,
            series: $series,
            audioFileName: new AudioFileName(
                $fromRecord->audio_file_name,
            ),
            id: Uuid::fromString($fromRecord->id),
        );
    }

    public function createMessages(MessageRecords $fromRecords): Messages
    {
        $speakerIds = array_values(array_unique(array_filter(
            $fromRecords->mapToArray(
                static fn (MessageRecord $r) => $r->speaker_profile_id,
            ),
            function (string|null $id) {
                if ($id === null) {
                    return false;
                }

                return $this->possibleSpeakers->findById($id) === null;
            },
        )));

        if (count($speakerIds) > 0) {
            $this->possibleSpeakers = $this->possibleSpeakers->withAddedProfiles(
                $this->profileRepository->findByIds($speakerIds),
            );
        }

        $seriesIds = array_values(array_unique(array_filter(
            $fromRecords->mapToArray(
                static fn (MessageRecord $r) => $r->series_id,
            ),
            function (string|null $id) {
                if ($id === null) {
                    return false;
                }

                return $this->possibleSeries->findById($id) === null;
            },
        )));

        if (count($seriesIds) > 0) {
            $this->possibleSeries = $this->possibleSeries->withAddedSeriesCollection(
                $this->messageSeriesRepository->findByIds($seriesIds),
            );
        }

        return new Messages($fromRecords->mapToArray(
            fn (MessageRecord $record) => $this->createMessage(
                $record,
            ),
        ));
    }
}
