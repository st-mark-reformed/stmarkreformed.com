<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessages;
use App\InternalSeries\EmptyInternalSeries;
use App\InternalSeries\InternalSeries;
use App\InternalSeries\InternalSeriesCollection;
use App\InternalSeries\InternalSeriesRepository;
use App\Profiles\EmptyProfile;
use App\Profiles\Profile;
use App\Profiles\Profiles;
use App\Profiles\ProfilesRepository;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class Transformer
{
    public function __construct(
        private readonly InternalSeriesRepository $seriesRepository,
        private readonly ProfilesRepository $profilesRepository,
    ) {
    }

    public function toEntity(InternalMessageRecord $record): InternalMessage
    {
        return new InternalMessage(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            title: $record->title,
            slug: $record->slug,
            audioPath: $record->audio_path,
            audioFileSize: $record->audio_file_size,
            speaker: $this->findSpeaker(speakerId: $record->speaker_id),
            passage: $record->passage,
            series: $this->findSeries(seriesId: $record->series_id),
            description: $record->description,
        );
    }

    public function toEntities(InternalMessagesRecords $records): InternalMessages
    {
        return new InternalMessages(
            items: $records->map(
                callback: fn (InternalMessageRecord $r) => $this->toEntity(
                    record: $r,
                ),
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
                new DateTimeZone('US/Central'),
            );
        } catch (Throwable) {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d',
                '1900-01-01',
                new DateTimeZone('US/Central'),
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
            return new EmptyProfile();
        }

        return $profile;
    }

    private InternalSeriesCollection $seriesCollection;

    private function findSeries(string $seriesId): InternalSeries
    {
        if (! isset($this->seriesCollection)) {
            $this->seriesCollection = $this->seriesRepository->findAll();
        }

        $series = $this->seriesCollection->findById(id: $seriesId);

        if ($series === null) {
            return new EmptyInternalSeries();
        }

        return $series;
    }
}
