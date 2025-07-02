<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

use Redis;

use function array_map;
use function is_array;
use function is_string;
use function json_decode;

readonly class RedisRepository
{
    public function __construct(private Redis $redis)
    {
    }

    /** @return array<>|null */

    /** @return array{totalPages: int, entries: list<Message>}|null */
    public function findAllMessagesByPage(int $page): array|null
    {
        $pageDataString = $this->redis->get(
            'messages:page:' . ((string) $page),
        );

        if (! is_string($pageDataString)) {
            return null;
        }

        $pageData = json_decode(
            $pageDataString,
            true,
        );

        if (! is_array($pageData)) {
            return null;
        }

        return [
            'totalPages' => $pageData['totalPages'],
            'entries' => array_map(
                static function (array $item): Message {
                    $by = null;

                    if (is_array($item['by'])) {
                        $by = new MessageSeriesOrBy(
                            title: $item['by']['title'],
                            slug: $item['by']['slug'],
                        );
                    }

                    $series = null;

                    if (is_array($item['series'])) {
                        $series = new MessageSeriesOrBy(
                            title: $item['series']['title'],
                            slug: $item['series']['slug'],
                        );
                    }

                    return new Message(
                        uid: $item['uid'],
                        title: $item['title'],
                        slug: $item['slug'],
                        postDate: $item['postDate'],
                        by: $by,
                        text: (string) $item['text'],
                        series: $series,
                        audioFileName: $item['audioFileName'],
                    );
                },
                $pageData['entries'],
            ),
        ];
    }
}
