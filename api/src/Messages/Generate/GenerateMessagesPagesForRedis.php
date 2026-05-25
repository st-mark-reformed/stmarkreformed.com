<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Messages\Message;
use App\Messages\MessagesRepository;

readonly class GenerateMessagesPagesForRedis
{
    private const int PER_PAGE = 25;

    private const int MOST_RECENT_SERIES_LIMIT = 6;

    public function __construct(
        private MessagesRepository $repository,
        private MessagesPagesBuilder $messagesPagesBuilder,
        private BySpeakerPagesBuilder $bySpeakerPagesBuilder,
        private BySeriesPagesBuilder $bySeriesPagesBuilder,
        private MostRecentSeriesBuilder $mostRecentSeriesBuilder,
        private BySpeakerOptionsBuilder $bySpeakerOptionsBuilder,
        private BySeriesOptionsBuilder $bySeriesOptionsBuilder,
    ) {
    }

    public function generate(): void
    {
        $messages = $this->repository->findAll()->filter(
            callback: static fn (Message $message): bool => $message->isEnabled,
        );

        $this->messagesPagesBuilder->build(
            messages: $messages,
            perPage: self::PER_PAGE,
        );

        foreach ($messages->distinctSpeakers() as $speaker) {
            $this->bySpeakerPagesBuilder->build(
                speaker: $speaker,
                messages: $messages->bySpeakerId(id: $speaker->id),
                perPage: self::PER_PAGE,
            );
        }

        foreach ($messages->distinctSeries() as $series) {
            $this->bySeriesPagesBuilder->build(
                series: $series,
                messages: $messages->bySeriesId(id: $series->id),
                perPage: self::PER_PAGE,
            );
        }

        $this->mostRecentSeriesBuilder->build(
            seriesNewestFirst: $messages->distinctSeries(),
            limit: self::MOST_RECENT_SERIES_LIMIT,
        );

        $this->bySpeakerOptionsBuilder->build(
            speakersWithMessages: $messages->distinctSpeakers(),
        );

        $this->bySeriesOptionsBuilder->build(
            seriesWithMessages: $messages->distinctSeries(),
        );
    }
}
