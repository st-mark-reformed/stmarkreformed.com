<?php

declare(strict_types=1);

namespace App;

use function explode;
use function lcfirst;
use function preg_split;
use function ucfirst;

readonly class CaseConversion
{
    public function toPascale(string $string): string
    {
        return $this->spaceConvert(
            $this->underscoreConvert($string),
        );
    }

    public function toCamel(string $string): string
    {
        return lcfirst($this->toPascale($string));
    }

    private function underscoreConvert(string $string): string
    {
        $finalStr = '';
        foreach (explode('_', $string) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }

    private function spaceConvert(string $string): string
    {
        $finalStr = '';

        /** @var array<int, string> $stringArray */
        $stringArray = preg_split('/\s+/', $string);

        foreach ($stringArray as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }
}
