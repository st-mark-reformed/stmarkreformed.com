<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\AudioFileName;
use App\Messages\Message\Message;
use App\Messages\Message\Title;
use App\Messages\Series\MessageSeriesRepository;
use App\Profiles\ProfileRepository;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

use function is_array;
use function is_bool;

readonly class MessageEntityFactory
{
    public function __construct(
        private ProfileRepository $profileRepository,
        private MessageSeriesRepository $seriesRepository,
    ) {
    }

    public function fromServerRequest(ServerRequestInterface $request): Message
    {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        $isPublished = $submittedData['published'] ?? false;
        $isPublished = is_bool($isPublished) ? $isPublished : false;

        $date = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s.v\Z',
            $submittedData['date'] ?? false,
        );

        if (! ($date instanceof DateTimeImmutable)) {
            $date = null;
        } else {
            $date = $date->setTimezone(
                new DateTimeZone('UTC'),
            );
        }

        try {
            $speakerId = Uuid::fromString(
                $submittedData['speakerId'] ?? '',
            );
        } catch (Throwable) {
            $speakerId = Uuid::uuid6();
        }

        $speaker = $this->profileRepository->findById($speakerId);

        try {
            $seriesId = Uuid::fromString(
                $submittedData['seriesId'] ?? '',
            );
        } catch (Throwable) {
            $seriesId = Uuid::uuid6();
        }

        $series = $this->seriesRepository->findById($seriesId);

        return new Message(
            isPublished: $isPublished,
            date: $date,
            title: new Title(
                $submittedData['title'] ?? '',
            ),
            text: $submittedData['text'] ?? '',
            speaker: $speaker,
            series: $series,
            audioFileName: new AudioFileName(
                $submittedData['audioFile'] ?? '',
            ),
        );
    }
}
