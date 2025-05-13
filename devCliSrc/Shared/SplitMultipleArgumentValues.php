<?php

declare(strict_types=1);

namespace Cli\Shared;

use function array_merge;
use function array_values;
use function explode;

readonly class SplitMultipleArgumentValues
{
    /**
     * @param string[] $input
     *
     * @return string[]
     */
    public function split(array $input): array
    {
        $split = [];

        foreach ($input as $i) {
            $split = array_values(array_merge(
                $split,
                explode('|', $i),
            ));
        }

        return $split;
    }
}
