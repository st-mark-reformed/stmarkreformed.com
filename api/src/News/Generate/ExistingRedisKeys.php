<?php

declare(strict_types=1);

namespace App\News\Generate;

readonly class ExistingRedisKeys
{
    /** @var string[] */
    public array $pageKeys;

    /** @var string[] */
    public array $slugKeys;

    /** @param string[] $allKeys */
    public function __construct(array $allKeys)
    {
        $pageKeys = [];
        $slugKeys = [];

        foreach ($allKeys as $key) {
            if (NewsRedisKey::isPageKey($key)) {
                $pageKeys[] = $key;

                continue;
            }

            if (! NewsRedisKey::isSlugKey($key)) {
                continue;
            }

            $slugKeys[] = $key;
        }

        $this->pageKeys = $pageKeys;
        $this->slugKeys = $slugKeys;
    }
}
