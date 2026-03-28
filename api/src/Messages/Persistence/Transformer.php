<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message;
use App\Messages\Messages;
use App\Profiles\Profile;
use App\Profiles\Profiles;
use App\Profiles\ProfilesRepository;
use App\Series\Series;
use App\Series\SeriesCollection;
use App\Series\SeriesRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class Transformer
{
    public function __construct(
        private readonly SeriesRepository $seriesRepository,
        private readonly ProfilesRepository $profilesRepository,
    ) {
    }

    public function toEntity(MessageRecord $record): Message
    {
        return new Message(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            title: $record->title,
            slug: $record->slug,
            audioPath: $record->audio_path,
            speaker: $this->findSpeaker(speakerId: $record->speaker_id),
            passage: $record->passage,
            series: $this->findSeries(seriesId: $record->series_id),
            description: $record->description,
        );
    }

    public function toEntities(MessagesRecords $records): Messages
    {
        return new Messages(
            items: $records->map(
                callback: fn (MessageRecord $r) => $this->toEntity(record: $r),
            ),
        );
    }

    private function createDate(string $date): DateTimeImmutable
    {
        try {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $date,
            );
        } catch (Throwable) {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d',
                '1900-01-01',
            );
        }
    }

    private Profiles $profiles;

    private function findSpeaker(string $speakerId): Profile
    {
        if (! isset($this->profiles)) {
            $this->profiles = $this->profilesRepository->findAll();
        }

        $profile = $this->profiles->findById(id: $speakerId);

        if ($profile === null) {
            return new Profile();
        }

        return $profile;
    }

    private SeriesCollection $seriesCollection;

    private function findSeries(string $seriesId): Series
    {
        if (! isset($this->seriesCollection)) {
            $this->seriesCollection = $this->seriesRepository->findAll();
        }

        $series = $this->seriesCollection->findById(id: $seriesId);

        if ($series === null) {
            return new Series();
        }

        return $series;
    }
}
