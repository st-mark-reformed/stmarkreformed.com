<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Messages\Message;
use App\Messages\MessagesRepository;
use App\Messages\SeriesMessages;
use App\Messages\SpeakerMessages;
use Redis;
use Throwable;

use function array_map;

readonly class GenerateMessagesPagesForRedis
{
    public const string JOB_HANDLE = 'generate-messages-redis-pages';

    public const string JOB_NAME = 'Generate Messages Redis Pages';

    private const int PER_PAGE = 25;

    private const int MOST_RECENT_SERIES_LIMIT = 6;

    public function __construct(
        private Redis $redis,
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

        // Scan and pre-categorize existing keys upfront so the write phase
        // can run inside a single pipelined batch. One global KEYS call
        // replaces what used to be ~125 per-builder scans.
        $existing = new ExistingRedisKeys(
            allKeys: $this->redis->keys(MessagesRedisKey::allPattern()),
        );

        $speakerGroups = $messages->groupBySpeaker();
        $seriesGroups  = $messages->groupBySeries();

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->messagesPagesBuilder->build(
                messages: $messages,
                perPage: self::PER_PAGE,
                existing: $existing,
            );

            foreach ($speakerGroups as $group) {
                $this->bySpeakerPagesBuilder->build(
                    speaker: $group->speaker,
                    messages: $group->messages,
                    perPage: self::PER_PAGE,
                    existing: $existing,
                );
            }

            foreach ($seriesGroups as $group) {
                $this->bySeriesPagesBuilder->build(
                    series: $group->series,
                    messages: $group->messages,
                    perPage: self::PER_PAGE,
                    existing: $existing,
                );
            }

            $this->mostRecentSeriesBuilder->build(
                seriesNewestFirst: array_map(
                    static fn (SeriesMessages $g) => $g->series,
                    $seriesGroups,
                ),
                limit: self::MOST_RECENT_SERIES_LIMIT,
            );

            $this->bySpeakerOptionsBuilder->build(
                speakersWithMessages: array_map(
                    static fn (SpeakerMessages $g) => $g->speaker,
                    $speakerGroups,
                ),
            );

            $this->bySeriesOptionsBuilder->build(
                seriesWithMessages: array_map(
                    static fn (SeriesMessages $g) => $g->series,
                    $seriesGroups,
                ),
            );
        } catch (Throwable $error) {
            $this->redis->discard();

            throw $error;
        }

        $this->redis->exec();
    }
}
