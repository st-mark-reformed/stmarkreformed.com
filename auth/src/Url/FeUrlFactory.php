<?php

declare(strict_types=1);

namespace App\Url;

use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\RuntimeConfig;

use function implode;
use function is_array;
use function rtrim;
use function trim;

readonly class FeUrlFactory
{
    private string $feUrl;

    public function __construct(RuntimeConfig $config)
    {
        $this->feUrl = $config->getString(
            RuntimeConfigOptions::FE_URL,
        );
    }

    /**
     * @param string[]|string       $uri
     * @param array<string, string> $queryString
     */
    public function create(
        string|array $uri = '',
        array $queryString = [],
    ): Url {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        return new Url(
            rtrim($this->feUrl, '/'),
            '/' . trim($uri, '/'),
            $queryString,
        );
    }
}
