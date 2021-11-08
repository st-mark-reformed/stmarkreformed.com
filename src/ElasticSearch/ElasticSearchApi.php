<?php

declare(strict_types=1);

namespace App\ElasticSearch;

use App\ElasticSearch\SetUpIndices\SetUpIndices;

class ElasticSearchApi
{
    public function __construct(
        private SetUpIndices $setUpIndices,
    ) {
    }

    public function setUpIndices(): void
    {
        $this->setUpIndices->setUp();
    }
}
