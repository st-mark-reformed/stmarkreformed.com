<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessagesRepository;
use Redis;
use Throwable;

readonly class GenerateInternalMediaPagesForRedis
{
    public const string JOB_HANDLE = 'generate-internal-media-redis-pages';

    public const string JOB_NAME = 'Generate Internal Media Redis Pages';

    private const int PER_PAGE = 25;

    public function __construct(
        private Redis $redis,
        private InternalMessagesRepository $repository,
        private InternalMediaPagesBuilder $pagesBuilder,
        private BySpeakerInternalMediaPagesBuilder $bySpeakerPagesBuilder,
        private BySeriesInternalMediaPagesBuilder $bySeriesPagesBuilder,
    ) {
    }

    public function generate(): void
    {
        $messages = $this->repository->findAll()->filter(
            callback: static fn (InternalMessage $message): bool => $message->isEnabled,
        );

        $existing = new ExistingInternalMediaRedisKeys(
            allKeys: $this->redis->keys(InternalMediaRedisKey::allPattern()),
        );

        $speakerGroups = $messages->groupBySpeaker();
        $seriesGroups  = $messages->groupBySeries();

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->pagesBuilder->build(
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
        } catch (Throwable $error) {
            $this->redis->discard();

            throw $error;
        }

        $this->redis->exec();
    }
}
