<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\EditInternalMessage\PostEditInternalMessage;

use App\EmptyUuid;
use App\InternalMessages\InternalMessage;
use App\InternalSeries\EmptyInternalSeries;
use App\InternalSeries\InternalSeries;
use App\InternalSeries\InternalSeriesRepository;
use App\Profiles\EmptyProfile;
use App\Profiles\Profile;
use App\Profiles\ProfilesRepository;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class InternalMessageFactory
{
    public function __construct(
        private InternalSeriesRepository $seriesRepository,
        private ProfilesRepository $profilesRepository,
    ) {
    }

    public function createFromRequest(ServerRequest $request): InternalMessage
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString(name: 'internalMessageId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new InternalMessage(
            id: $id,
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $this->getDate(
                date: $request->parsedBody->getString(name: 'date'),
            ),
            title: $request->parsedBody->getString(name: 'title'),
            audioPath: $request->parsedBody->getString(name: 'audioPath'),
            speaker: $this->getSpeaker(
                profileId: $request->parsedBody->getString(name: 'speakerId'),
            ),
            passage: $request->parsedBody->getString(name: 'passage'),
            series: $this->getSeries(
                seriesId: $request->parsedBody->getString(name: 'seriesId'),
            ),
        );
    }

    private function getDate(string $date): DateTimeImmutable
    {
        $dateObj = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date,
            new DateTimeZone('US/Central'),
        );

        if ($dateObj === false) {
            $dateObj = DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $date,
                new DateTimeZone('US/Central'),
            );
        }

        if ($dateObj === false) {
            $dateObj = new DateTimeImmutable()->setTimezone(
                new DateTimeZone('US/Central'),
            );
        }

        return $dateObj;
    }

    private function getSpeaker(string $profileId): Profile
    {
        try {
            return $this->profilesRepository->findById(id: $profileId)->profile;
        } catch (Throwable) {
            return new EmptyProfile();
        }
    }

    private function getSeries(string $seriesId): InternalSeries
    {
        try {
            return $this->seriesRepository->findById(id: $seriesId)->series;
        } catch (Throwable) {
            return new EmptyInternalSeries();
        }
    }
}
