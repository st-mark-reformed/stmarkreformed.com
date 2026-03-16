<?php

declare(strict_types=1);

namespace App\Url;

use function array_keys;
use function array_map;
use function count;
use function http_build_query;
use function implode;

readonly class Url
{
    /** @param array<string, string> $query */
    public function __construct(
        private string $url,
        private string $uri,
        private array $query,
    ) {
        array_map(
            static fn (string $key) => $key,
            array_keys($query),
        );

        array_map(
            static fn (string $val) => $val,
            $query,
        );
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function asString(): string
    {
        $url = [
            $this->url,
            $this->uri,
        ];

        if (count($this->query) > 0) {
            $url[] = '?' . http_build_query($this->query);
        }

        return implode('', $url);
    }
}
