<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

readonly class ExistingRedisKeys
{
    /** @var string[] */
    public array $slugKeys;

    /** @param string[] $allKeys */
    public function __construct(array $allKeys)
    {
        $slugKeys = [];

        foreach ($allKeys as $key) {
            if (! MenOfTheMarkRedisKey::isSlugKey($key)) {
                continue;
            }

            $slugKeys[] = $key;
        }

        $this->slugKeys = $slugKeys;
    }
}
