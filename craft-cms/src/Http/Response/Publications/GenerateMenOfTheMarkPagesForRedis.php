<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use Redis;

class GenerateMenOfTheMarkPagesForRedis
{
    public function __construct(
        private Redis $redis,
        private FetchMenOfTheMark $fetchMenOfTheMark,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    private array $pageSlugKeys = [];

    public function generate(): void
    {
        $result = $this->fetchMenOfTheMark->fetch();

        $this->redis->set(
            'publications:men_of_the_mark:index',
            json_encode([
                'entries' => $result->mapItems(
                    function (Publication $publication): array {
                        $this->generateSlugPage($publication);

                        return $this->publicationJson($publication);
                    }
                ),
            ]),
        );

        $existingSlugKeys = $this->redis->keys(
            'publications:men_of_the_mark:slug:*'
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

    private function publicationJson(Publication $publication): array
    {
        return [
            'uid' => $publication->uid,
            'title' => $publication->title,
            'slug' => $publication->slug,
            'bodyHtml' => $publication->bodyHtml,
            'publicationDate' => $publication->publicationDate->format(
                'Y-m-d H:i:s',
            ),
        ];
    }

    private function generateSlugPage(Publication $publication): void
    {
        $key = 'publications:men_of_the_mark:slug:' . $publication->slug;

        $this->pageSlugKeys[] = $key;

        $this->redis->set($key, json_encode([
            'entry' => $this->publicationJson($publication),
        ]));
    }
}
