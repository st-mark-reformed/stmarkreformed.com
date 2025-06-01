<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resource\ResourceDownloadItem;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use Redis;

class GenerateResourcePagesForRedis
{
    private const PER_PAGE = 12;

    public function __construct(
        private Redis $redis,
        private EntryQueryFactory $entryQueryFactory,
        private RetrieveResources $retrieveResources,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    private array $pageSlugKeys = [];

    public function generate(): void
    {
        $this->pageSlugKeys = [];

        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('resources')
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'resources:page:' . $page;
            $this->generatePage(
                $pagination->withCurrentPage($page)
            );
        }

        $existingPageKeys = $this->redis->keys(
            'resources:page:*'
        );

        foreach ($existingPageKeys as $key) {
            if (!in_array($key, $generatedKeys, true)) {
                $this->redis->del($key);
            }
        }

        $existingSlugKeys = $this->redis->keys(
            'resources:slug:*'
        );

        foreach ($existingSlugKeys as $key) {
            if (!in_array($key, $this->pageSlugKeys, true)) {
                $this->redis->del($key);
            }
        }
    }

    private function createJsonArrayFromResourceItem(
        ResourceItem $resourceItem,
    ): array {
        return [
            'title' => $resourceItem->title,
            'slug' => $resourceItem->slug,
            'body' => (string) $resourceItem->body,
            'resourceDownloads' => array_map(
                static fn (ResourceDownloadItem $item) => [
                    'filename' => $item->filename(),
                ],
                $resourceItem->resourceDownloads,
            ),
        ];
    }

    private function generatePage(Pagination $pagination): void
    {
        $results = $this->retrieveResources->retrieve(pagination: $pagination);

        $entries = $results->mapItems(function (ResourceItem $resourceItem) {
            return $this->createJsonArrayFromResourceItem(
                $resourceItem,
            );
        });

        array_map(
            function (array $entry) {
                $key = 'resources:slug:' . $entry['slug'];

                $this->pageSlugKeys[] = $key;

                $this->redis->set(
                    $key,
                    json_encode([
                        'entry' => $entry,
                    ]),
                );
            },
            $entries,
        );

        $this->redis->set(
            'resources:page:' . $pagination->currentPage(),
            json_encode([
                'currentPage' => $pagination->currentPage(),
                'perPage' => $pagination->perPage(),
                'totalResults' => $pagination->totalResults(),
                'totalPages' => $pagination->totalPages(),
                'pagesArray' => $pagination->pagesArray(),
                'prevPageLink' => $pagination->prevPageLink(),
                'nextPageLink' => $pagination->nextPageLink(),
                'firstPageLink' => $pagination->firstPageLink(),
                'lastPageLink' => $pagination->lastPageLink(),
                'entries' => $entries,
            ]),
        );
    }
}
