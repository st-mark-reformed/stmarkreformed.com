<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\PostEditMessage;

use App\EmptyUuid;
use App\Messages\Message;
use App\Profiles\Profile;
use App\Profiles\ProfilesRepository;
use App\Series\Series;
use App\Series\SeriesRepository;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class MessageFactory
{
    public function __construct(
        private SeriesRepository $seriesRepository,
        private ProfilesRepository $profilesRepository,
    ) {
    }

    public function createFromRequest(ServerRequest $request): Message
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString(name: 'messageId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new Message(
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
            return new Profile();
        }
    }

    private function getSeries(string $seriesId): Series
    {
        try {
            return $this->seriesRepository->findById(id: $seriesId)->series;
        } catch (Throwable) {
            return new Series();
        }
    }
}
