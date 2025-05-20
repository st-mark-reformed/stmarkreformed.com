<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use Redis;

class GenerateHymnsOfTheMonthPageForRedis
{
    public function __construct(
        private Redis $redis,
        private RetrieveHymns $retrieveHymns,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    private array $pageSlugKeys = [];

    public function generate(): void
    {
        $results = $this->retrieveHymns->retrieve();

        $this->redis->set(
            'members:hymns_of_the_month:index',
            json_encode([
                'entries' => $results->mapItems(
                    function (HymnItem $hymnItem): array {
                        $this->generateSlugPage($hymnItem);

                        return $this->hymnJson($hymnItem);
                    }
                ),
            ]),
        );

        $existingSlugKeys = $this->redis->keys(
            'members:hymns_of_the_month:slug:*',
        );

        foreach ($existingSlugKeys as $key) {
            if (!in_array(
                $key,
                $this->pageSlugKeys,
                true,
            )) {
                $this->redis->del($key);
            }
        }
    }

    public function hymnJson(HymnItem $hymnItem): array
    {
        return [
            'title' => $hymnItem->title(),
            'slug' => $hymnItem->slug(),
            'hymnPsalmName' => $hymnItem->hymnPsalmName(),
            'content'=> $hymnItem->content(),
            'musicSheetFilePath' => $hymnItem->musicSheetFilePath(),
            'practiceTracks' => array_map(
                function (HymnItemPracticeTrack $practiceTrack): array {
                    return [
                        'title' => $practiceTrack->title,
                        'path' => $practiceTrack->path,
                    ];
                },
                $hymnItem->practiceTracks(),
            ),
        ];
    }

    public function generateSlugPage(HymnItem $hymnItem): void
    {
        $key = 'members:hymns_of_the_month:slug:' . $hymnItem->slug();

        $this->pageSlugKeys[] = $key;

        $this->redis->set($key, json_encode([
            'entry' => $this->hymnJson($hymnItem),
        ]));
    }
}
