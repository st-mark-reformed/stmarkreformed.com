<?php

declare(strict_types=1);

namespace App\Messages\GenerateMessagesPagesForRedis;

use App\Messages\Message\Message;
use App\Messages\MessageRepository;
use App\Messages\PublishStatusOption;
use App\Messages\Series\MessageSeries\MessageSeries;
use App\Pagination;
use App\Profiles\Profile\Profile;
use Redis;

use function in_array;
use function json_encode;

class GenerateMessagesPagesForRedis
{
    public const string JOB_HANDLE = 'generate-messages-pages-for-redis';

    public const string JOB_NAME = 'Generate Messages Pages For Redis';

    private const int PER_PAGE = 25;

    public function __construct(
        private readonly Redis $redis,
        private readonly MessageRepository $repository,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    private array $pageSlugKeys = [];

    private array $byIds = [];

    private array $seriesIds = [];

    public function generate(): void
    {
        $this->pageSlugKeys = [];
        $this->byIds        = [];
        $this->seriesIds    = [];

        // Prime the memo
        $this->repository->findAll();

        $results = $this->repository->findAllByLimit(
            limit: self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $pagination = (new Pagination())
            ->withPerPage(self::PER_PAGE)
            ->withCurrentPage(1)
            ->withTotalResults($results->absoluteTotalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'api-messages:page:' . $page;
            $this->generatePage(
                $pagination->withCurrentPage($page),
            );
        }

        $existingPageKeys = $this->redis->keys('api-messages:page:*');

        foreach ($existingPageKeys as $key) {
            if (
                in_array(
                    $key,
                    $generatedKeys,
                    true,
                )
            ) {
                continue;
            }

            $this->redis->del($key);
        }

        $existingSlugKeys = $this->redis->keys('api-messages:slug:*');

        foreach ($existingSlugKeys as $key) {
            if (
                in_array(
                    $key,
                    $this->pageSlugKeys,
                    true,
                )
            ) {
                continue;
            }

            $this->redis->del($key);
        }
    }

    private function generatePage(Pagination $pagination): void
    {
        $currentPage = $pagination->currentPage();

        $results = $this->repository->findAllByLimit(
            limit: self::PER_PAGE,
            offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $results->messages->walk(
            function (Message $message): void {
                $key = 'api-messages:slug:' . $message->slug->slug;

                $this->pageSlugKeys[] = $key;

                $this->generateByPages($message->speaker);

                $this->generateSeriesPages($message->series);

                $this->redis->set(
                    $key,
                    json_encode([
                        'message' => $message->asScalar(),
                    ]),
                );
            },
        );

        $this->redis->set(
            'api-messages:page:' . $currentPage,
            json_encode([
                'currentPage' => $currentPage,
                'perPage' => $pagination->perPage(),
                'totalResults' => $pagination->totalResults(),
                'totalPages' => $pagination->totalPages(),
                'pagesArray' => $pagination->pagesArray(),
                'prevPageLink' => $pagination->prevPageLink(),
                'nextPageLink' => $pagination->nextPageLink(),
                'firstPageLink' => $pagination->firstPageLink(),
                'lastPageLink' => $pagination->lastPageLink(),
                'messages' => $results->messages->asScalar(),
            ]),
        );
    }

    private function generateByPages(Profile|null $speaker): void
    {
        if (
            $speaker === null ||
            isset($this->byIds[$speaker->id->toString()])
        ) {
            return;
        }

        $profileSlug = $speaker->slug->slug;

        $results = $this->repository->findAllBySpeakerByLimit(
            speakerId: $speaker->id,
            limit: self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $pagination = (new Pagination())
            ->withPerPage(self::PER_PAGE)
            ->withCurrentPage(1)
            ->withTotalResults($results->absoluteTotalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'api-messages:by:' . $profileSlug . ':' . $page;
            $this->generateByPage(
                $pagination->withCurrentPage($page),
                $speaker,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'api-messages:by:' . $profileSlug . ':*',
        );

        foreach ($existingPageKeys as $key) {
            if (
                in_array(
                    $key,
                    $generatedKeys,
                    true,
                )
            ) {
                continue;
            }

            $this->redis->del($key);
        }

        $this->byIds[$speaker->id->toString()] = $speaker->id->toString();
    }

    private function generateByPage(
        Pagination $pagination,
        Profile $profile,
    ): void {
        $currentPage = $pagination->currentPage();

        $results = $this->repository->findAllBySpeakerByLimit(
            speakerId: $profile->id,
            limit: self::PER_PAGE,
            offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $this->redis->set(
            'api-messages:by:' . $profile->slug->slug . ':' . $currentPage,
            json_encode([
                'currentPage' => $currentPage,
                'perPage' => $pagination->perPage(),
                'totalResults' => $pagination->totalResults(),
                'totalPages' => $pagination->totalPages(),
                'pagesArray' => $pagination->pagesArray(),
                'prevPageLink' => $pagination->prevPageLink(),
                'nextPageLink' => $pagination->nextPageLink(),
                'firstPageLink' => $pagination->firstPageLink(),
                'lastPageLink' => $pagination->lastPageLink(),
                'messages' => $results->messages->asScalar(),
                'byName' => $profile->fullNameWithHonorific,
                'bySlug' => $profile->slug->slug,
            ]),
        );
    }

    private function generateSeriesPages(MessageSeries|null $series): void
    {
        if (
            $series === null ||
            isset($this->seriesIds[$series->id->toString()])
        ) {
            return;
        }

        $seriesSlug = $series->slug->slug;

        $results = $this->repository->findAllInSeriesByLimit(
            seriesId: $series->id,
            limit: self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $pagination = (new Pagination())
            ->withPerPage(self::PER_PAGE)
            ->withCurrentPage(1)
            ->withTotalResults($results->absoluteTotalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'api-messages:series:' . $seriesSlug . ':' . $page;
            $this->generateSeriesPage(
                $pagination->withCurrentPage($page),
                $series,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'api-messages:series:' . $seriesSlug . ':*',
        );

        foreach ($existingPageKeys as $key) {
            if (
                in_array(
                    $key,
                    $generatedKeys,
                    true,
                )
            ) {
                continue;
            }

            $this->redis->del($key);
        }

        $this->seriesIds[$series->id->toString()] = $series->id->toString();
    }

    private function generateSeriesPage(
        Pagination $pagination,
        MessageSeries $series,
    ): void {
        $currentPage = $pagination->currentPage();

        $results = $this->repository->findAllInSeriesByLimit(
            seriesId: $series->id,
            limit: self::PER_PAGE,
            offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        $this->redis->set(
            'api-messages:series:' . $series->slug->slug . ':' . $currentPage,
            json_encode([
                'currentPage' => $currentPage,
                'perPage' => $pagination->perPage(),
                'totalResults' => $pagination->totalResults(),
                'totalPages' => $pagination->totalPages(),
                'pagesArray' => $pagination->pagesArray(),
                'prevPageLink' => $pagination->prevPageLink(),
                'nextPageLink' => $pagination->nextPageLink(),
                'firstPageLink' => $pagination->firstPageLink(),
                'lastPageLink' => $pagination->lastPageLink(),
                'messages' => $results->messages->asScalar(),
                'seriesName' => $series->title->title,
                'seriesSlug' => $series->slug->slug,
            ]),
        );
    }
}
