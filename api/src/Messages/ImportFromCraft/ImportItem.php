<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

use App\Messages\Message\AudioFileName;
use App\Messages\Message\Message as MessageEntity;
use App\Messages\Message\Slug;
use App\Messages\Message\Title;
use App\Messages\MessageRepository;
use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\MessageSeries\Slug as SeriesSlug;
use App\Messages\Series\MessageSeries\Title as SeriesTitle;
use App\Messages\Series\MessageSeriesRepository;
use App\Profiles\Profile\Profile;
use App\Profiles\ProfileRepository;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

use function dd;
use function var_dump;

class ImportItem
{
    /** @var Profile[]|null[] */
    private array $speakers = [];

    /** @var MessageSeries[] */
    private array $series = [];

    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly ImportStorageHandler $storageHandler,
        private readonly MessageSeriesRepository $messageSeriesRepository,
    ) {
    }

    public function import(Message $message): void
    {
        if ($this->storageHandler->uidHasAlreadyImported($message->uid)) {
            return;
        }

        $messageEntity = new MessageEntity(
            isPublished: true,
            date: $this->setDate($message),
            title: new Title($message->title),
            slug: new Slug($message->slug),
            text: $message->text,
            speaker: $this->setSpeaker($message),
            series: $this->setSeries($message),
            audioFileName: new AudioFileName(
                $message->audioFileName,
            ),
        );

        $result = $this->messageRepository->createAndPersist(
            $messageEntity,
        );

        if (! $result->success) {
            try {
                dd($message, $result->messages);
            } catch (Throwable) {
                var_dump($message);
                var_dump($result->messages);
                die;
            }
        }

        $this->storageHandler->writeUidAsImported($message->uid);
    }

    private function setDate(Message $message): DateTimeImmutable
    {
        $date = new DateTimeImmutable(
            $message->postDate,
            new DateTimeZone('America/Chicago'),
        );

        return $date->setTimezone(
            new DateTimeZone('UTC'),
        );
    }

    private function setSpeaker(Message $message): Profile|null
    {
        if ($message->by === null) {
            return null;
        }

        if (isset($this->speakers[$message->by->slug])) {
            return $this->speakers[$message->by->slug];
        }

        $speaker = $this->profileRepository->findByFullNameWithHonorific(
            $message->by->title,
        );

        $this->speakers[$message->by->slug] = $speaker;

        return $speaker;
    }

    private function setSeries(Message $message): MessageSeries|null
    {
        if ($message->series === null) {
            return null;
        }

        if (isset($this->series[$message->series->slug])) {
            return $this->series[$message->series->slug];
        }

        $series = $this->messageSeriesRepository->findBySlug(
            $message->series->slug,
        );

        if ($series === null) {
            $this->messageSeriesRepository->createAndPersist(
                new MessageSeries(
                    title: new SeriesTitle($message->series->title),
                    slug: new SeriesSlug($message->series->slug),
                ),
            );

            $series = $this->messageSeriesRepository->findBySlug(
                $message->series->slug,
            );
        }

        if ($series === null) {
            return null;
        }

        $this->series[$message->series->slug] = $series;

        return $series;
    }
}
