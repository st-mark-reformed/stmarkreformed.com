<?php

declare(strict_types=1);

namespace App\Resources\Generate;

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
            if (ResourcesRedisKey::isPageKey($key)) {
                $pageKeys[] = $key;

                continue;
            }

            if (! ResourcesRedisKey::isSlugKey($key)) {
                continue;
            }

            $slugKeys[] = $key;
        }

        $this->pageKeys = $pageKeys;
        $this->slugKeys = $slugKeys;
    }
}
