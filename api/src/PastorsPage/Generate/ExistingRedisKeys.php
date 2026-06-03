<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

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
            if (PastorsPageRedisKey::isPageKey($key)) {
                $pageKeys[] = $key;

                continue;
            }

            if (! PastorsPageRedisKey::isSlugKey($key)) {
                continue;
            }

            $slugKeys[] = $key;
        }

        $this->pageKeys = $pageKeys;
        $this->slugKeys = $slugKeys;
    }
}
